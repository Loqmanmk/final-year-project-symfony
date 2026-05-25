<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\AddToCartType;
use App\Service\CartHandler;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products/{slug}', name: 'product_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])] Product $product,
        Request $request,
        CartHandler $cartHandler,
    ): Response {
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('warning', 'Vous devez vous connecter pour ajouter un produit au panier.');

                return $this->redirectToRoute('app_login');
            }

            $cartHandler->add($product, (int) $form->get('quantity')->getData());
            $this->addFlash('success', 'Produit ajoute au panier.');

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
}
