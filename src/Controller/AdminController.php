<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
#[IsGranted('ROLE_SUPER_ADMIN')] // Seul le super administrateur a accès à l'ensemble des routes de ce controller
class AdminController extends AbstractController
{
    /**
     * Chargement de l'interface Super Administrateur.
     *
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/', name: 'admin_index')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('admin/index.html.twig', [
            'paniers' => $em->getRepository(Panier::class)->findNonPaid(), // Récupération de la liste des paniers non validés
            'users' => $em->getRepository(User::class)->findCreatedToday() // todo: Récupération de la liste des utilisateurs inscrits aujourd'hui
        ]);
    }
}
