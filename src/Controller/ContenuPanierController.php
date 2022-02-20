<?php

namespace App\Controller;

use App\Entity\ContenuPanier;
use App\Form\ContenuPanierType;
use App\Repository\ContenuPanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contenu/panier')]
class ContenuPanierController extends AbstractController
{
    /**
     * Suppression d'un élément du panier en cours.
     *
     * @param Request $request
     * @param ContenuPanier $contenuPanier
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'contenu_panier_delete', methods: ['POST'])]
    public function delete(Request $request, ContenuPanier $contenuPanier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenuPanier->getId(), $request->request->get('_token'))) {
            $entityManager->remove($contenuPanier);
            $entityManager->flush();

            $this->addFlash('success', 'cart.item_deleted');
        }

        return $this->redirectToRoute('panier_current', [], Response::HTTP_SEE_OTHER);
    }
}
