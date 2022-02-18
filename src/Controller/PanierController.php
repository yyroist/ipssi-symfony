<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Form\PanierType;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class PanierController extends AbstractController
{
    /**
     * Chargement du panier en cours de l'utilisateur.
     *
     * @param PanierRepository $repository
     * @param EntityManagerInterface $em
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/', name: 'panier_current', methods: ['GET'])]
    public function index(PanierRepository $repository, EntityManagerInterface $em): Response
    {
        // Récupération du dernier panier non payé de l'utilisateur
        $panier = $repository->findLastNonPaid($this->getUser());

        // Création du panier si non trouvé
        if (is_null($panier)) {
            $panier = new Panier();
            $panier->setUtilisateur($this->getUser()); // L'attribue automatiquement à la personne connectée

            // Sauvegarde en base de données
            $em->persist($panier);
            $em->flush();
        }

        return $this->render('panier/index.html.twig', ['panier' => $panier]);
    }

    #[Route('/{id}', name: 'panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/{id}/edit', name: 'panier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PanierType::class, $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('panier/edit.html.twig', [
            'panier' => $panier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'panier_delete', methods: ['POST'])]
    public function delete(Request $request, Panier $panier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($panier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_index', [], Response::HTTP_SEE_OTHER);
    }
}
