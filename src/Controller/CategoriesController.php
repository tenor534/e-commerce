<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app_')]
class CategoriesController extends AbstractController
{
   
    #[Route('/{slug}', name: 'products_list')]
    public function list(Categories $category): Response
    {
        //liste des produits de la catégorie
        $products = $category->getProducts();
        /*return $this->render(
            'categories/list.html.twig', [
            compact('category', 'products')
        ]);*/

        return $this->render(
            'categories/list.html.twig', [
            'category' => $category,
            'products' => $products,            
            'pageName' => 'Products list'             
        ]);
    }
}

