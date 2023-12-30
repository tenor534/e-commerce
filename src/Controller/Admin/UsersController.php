<?php

namespace App\Controller\Admin;

use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users', name: 'admin_users_')]
class UsersController extends AbstractController
{
    /**
     * Affiche l'identité de l'utilisateur
     *
     * @return Response
     */
    #[Route('/', name: 'index')]
    public function index(UsersRepository $usersRepository): Response
    {
        $users = $usersRepository->findBy(
            [], 
            ['firstname' => 'asc']
        );
        return $this->render('admin/users/index.html.twig',[
            'users' => $users, 
            'pageName' => 'Administration'
        ]);
    }
}