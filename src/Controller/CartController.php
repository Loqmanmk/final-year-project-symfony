<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Service\CartHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index', methods: ['GET'])]
    public function index(CartHandler $cartHandler): Response
    {
        return $this->render('cart/index.html.twig', [
            'lines' => $cartHandler->lines(),
            'total' => $cartHandler->total(),
        ]);
    }

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update(
        #[MapEntity(mapping: ['id' => 'id'])] Product $product,
        Request $request,
        CartHandler $cartHandler,
    ): Response {
        if (!$this->isCsrfTokenValid('cart_update_'.$product->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $cartHandler->update($product, $request->request->getInt('quantity', 1));
        $this->addFlash('success', 'Quantite mise a jour.');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(
        #[MapEntity(mapping: ['id' => 'id'])] Product $product,
        Request $request,
        CartHandler $cartHandler,
    ): Response {
        if (!$this->isCsrfTokenValid('cart_remove_'.$product->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $cartHandler->remove($product);
        $this->addFlash('success', 'Produit retire du panier.');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/save', name: 'cart_save', methods: ['POST'])]
    public function save(Request $request, CartHandler $cartHandler): Response
    {
        if (!$this->isCsrfTokenValid('cart_save', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ([] === $cartHandler->lines()) {
            $this->addFlash('warning', 'Votre panier est vide.');

            return $this->redirectToRoute('cart_index');
        }

        $user = $this->getUser();

        if (!$user instanceof User) {
            $this->addFlash('warning', 'Veuillez vous connecter pour enregistrer votre panier.');

            return $this->redirectToRoute('app_login');
        }

        $cartHandler->saveFor($user);
        $this->addFlash('success', 'Panier enregistré');

        return $this->redirectToRoute('profile_index');
    }
}
