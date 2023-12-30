<?php

namespace App\Controller\Admin;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/categories', name: 'admin_categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRipository): Response
    {

        $categories = $categoriesRipository->findBy(
            [], 
            ['categoryOrder' => 'asc']
        );

        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
            'pageName' => 'Categories administration'
        ]);   
    }
}