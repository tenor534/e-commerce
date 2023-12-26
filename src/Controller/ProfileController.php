<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/profile', name: 'app_')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Profile de l\'utilisateur',
            'pageName' => 'Profile'
        ]);
    }

    #[Route('/orders', name: 'orders')]
    public function orders(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Commandes de l user',
            'pageName' => 'Profile'
        ]);
    }
}
