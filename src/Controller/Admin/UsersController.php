<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users', name: 'admin_users')]
class UsersController extends AbstractController
{
    /**
     * Affiche l'identité de l'utilisateur
     *
     * @return Response
     */
    #[Route('/', name: 'admin_users')]
    public function index(): Response
    {
        return $this->render('admin/users/index.html.twig');
    }
}