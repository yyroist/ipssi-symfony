<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Chargement de la page d'accueil.
     *
     * @param ProduitRepository $repository
     * @return Response
     */
    #[Route('/', name: 'index')]
    public function index(ProduitRepository $repository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $repository->findAll() // Récupération de la liste de tous les produits
        ]);
    }
}
