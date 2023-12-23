<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products', name: 'app_products_')]
class ProductsController extends AbstractController
{
    #[Route('/{id}', name: 'details')]
    public function details(Products  $product): Response
    {
        //dd($product->getDescription());
        return $this->render('products/details.html.twig', compact('product'));
    }
    
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products  $product): Response
    {
        //dd($product->getDescription());
        //return $this->render('products/details.html.twig', compact('product'));
    }


}

