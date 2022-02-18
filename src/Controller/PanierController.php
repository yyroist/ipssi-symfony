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
    #[Route('/panier', name: 'panier_current', methods: ['GET'])]
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

    /**
     * Paiement du panier de l'utilisateur.
     *
     * @param Panier $panier
     * @param PanierRepository $repository
     * @param EntityManagerInterface $em
     * @return Response
     * @throws NonUniqueResultException
     */
    #[Route('/panier/{id}/paiement', name: 'panier_pay', methods: ['POST'])]
    public function pay(Panier $panier, PanierRepository $repository, EntityManagerInterface $em): Response
    {
        // Vérification que le panier en paramètre corresponde bien
        // au panier non payé en cours de l'utilisateur connecté
        $panierToPay = $repository->findLastNonPaid($this->getUser());
        if ($panierToPay->getId() !== $panier->getId()) {
            $this->addFlash('danger', 'cart.invalid');
        } else {

            // Passage du panier à l'état "payé"
            $panier->setEtat(true);
            $em->persist($panier);
            $em->flush();

            $this->addFlash('success', 'cart.paid');
        }

        // Redirection vers le panier
        return $this->redirectToRoute('panier_current');
    }

    /**
     * Affichage du contenu d'une commande.
     *
     * @param Panier $panier
     * @return Response
     */
    #[Route('/commande/{id}', name: 'panier_show', methods: ['GET'])]
    public function show(Panier $panier): Response
    {
        return $this->render('panier/show.html.twig', [
            'panier' => $panier,
        ]);
    }
}
