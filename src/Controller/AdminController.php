<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Chargement de l'interface Super Administrateur
     *
     * @return Response
     */
    #[Route('/', name: 'admin_index')]
    public function index(): Response
    {
        // todo: Récupération de la liste des paniers non validés
        // todo: Récupération de la liste des utilisateurs inscrits aujourd'hui

        return $this->render('admin/index.html.twig');
    }
}
