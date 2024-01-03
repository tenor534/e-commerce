<?php

namespace App\Controller\Admin;

use App\Form\UsersFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/show', name: 'show', methods: ['GET', 'POST'])]
    #[Route('/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function show(Request $request, EntityManagerInterface $entityManager): Response
    {
        $routeName = $request->attributes->get('_route');
        $user = $this->getUser();        
        
        $form = $this->createForm(UsersFormType::class, $user , [
            'disabled' => !($routeName === 'admin_users_edit')
        ]);
        $form->handleRequest($request);

        if ($routeName === 'admin_users_edit') {
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Profile mise à jour');
                return $this->redirectToRoute('admin_users_show', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('users/show.html.twig', [
            'user' => $user, 
            'userForm' => $form,
            'pageName' => 'My account'
        ]);
    }
}