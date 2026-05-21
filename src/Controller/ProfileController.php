<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile_index')]
    #[IsGranted('ROLE_USER')]
    public function index(CustomerOrderRepository $orderRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/index.html.twig', [
            'userProfile' => $user,
            'orders' => $orderRepository->findBy(['customer' => $user], ['createdAt' => 'DESC']),
        ]);
    }
}
