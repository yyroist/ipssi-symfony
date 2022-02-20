<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\ContenuPanierType;
use App\Form\ProduitType;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
     * Création d'un nouveau produit.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/new', name: 'produit_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de la photo du produit
            /** @var UploadedFile $brochureFile */
            $photo = $form->get('photo')->getData();
            if ($photo) {
                // Initialisation du nom du fichier à enregistrer
                $filename = uniqid(). '-' . time() . '.' . $photo->guessExtension();

                // Création du répertoire si non existant
                if (!is_dir($this->getParameter('produit_photo_directory'))) {
                    mkdir($this->getParameter('produit_photo_directory'), 0777, true);
                }

                // Copie du ficiher
                $photo->move(
                    $this->getParameter('produit_photo_directory'),
                    $filename
                );
                $produit->setPhoto($filename);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'product.added');
            return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * Chargement d'une fiche produit.
     *
     * @param Produit $produit
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PanierRepository $repository
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/{id}', name: 'produit_show', methods: ['GET', 'POST'])]
    public function show(Produit $produit, Request $request, EntityManagerInterface $entityManager, PanierRepository $repository): Response
    {
        $contenu_panier = new ContenuPanier();
        $form = $this->createForm(ContenuPanierType::class, $contenu_panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du dernier panier non payé de l'utilisateur
            $panier = $repository->findLastNonPaid($this->getUser());

            // Création du panier si non trouvé
            if (is_null($panier)) {
                $panier = new Panier();
                $panier->setUtilisateur($this->getUser()); // L'attribue automatiquement à la personne connectée

                // Sauvegarde en base de données
                $entityManager->persist($panier);
                $entityManager->flush();
            }

            // Vérification du stock : s'assurer que la quantité demandée soit inférieure ou égale au stock
            if ($contenu_panier->getQuantite() > $produit->getStock()) {
                $this->addFlash('danger', 'produit.stock_not_enough');
                return $this->redirectToRoute('produit_show', ['id' => $produit->getId()]);
            }

            $contenu_panier->setPanier($panier); // Affecte le produit ajouté au panier en cours non payé de l'utilisateur
            $contenu_panier->setProduit($produit); // Attribue le produit en cours automatiquement

            // Enregistrement des modifications
            $entityManager->persist($contenu_panier);
            $entityManager->flush();

            $this->addFlash('success', 'cart.product_added');
            return $this->redirectToRoute('panier_current', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'addToCart' => $form->createView()
        ]);


    }

    /**
     * Mise à jour d'une fiche produit.
     *
     * @param Request $request
     * @param Produit $produit
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/edit', name: 'produit_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d'un produit.
     *
     * @param Request $request
     * @param Produit $produit
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}/delete', name: 'produit_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();

            $this->addFlash('success', 'produit.deleted');
        } else {
            $this->addFlash('danger', 'produit_delete_error');
        }

        return $this->redirectToRoute('index', [], Response::HTTP_SEE_OTHER);
    }
}
