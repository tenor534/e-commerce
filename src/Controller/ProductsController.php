<?php

namespace App\Controller;

use App\Entity\Products;
use Knp\Menu\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products', name: 'app_products_')]
class ProductsController extends AbstractController
{
    #[Route('/{id}', name: 'details')]
    public function details(Products  $product, FactoryInterface $factory): Response
    {
        //dd($product->getDescription());
        return $this->render('products/details.html.twig', [
            'product'       => $product,                 
            'pageName'      => 'Details' 
        ]);
    }
    
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products  $product): Response
    {
        //dd($product->getDescription());
        //return $this->render('products/details.html.twig', compact('product'));
    }

    public function breadcrumbMenu(FactoryInterface $factory ): Response
    {
        $menu = $factory->createItem('root');

        // Ajouter les éléments du fil d'Ariane
        $menu->addChild('Accueil', ['route' => 'app_main']);
        
        //$menu->addChild('Informatique', ['route' => 'app_products_list', {id} => ]);
        //$menu->addChild('Ordinateur portable', ['route' => 'portable_route']);
        
        $menu->addChild('Lenovo h1252');

        // Créer la vue du menu
        $menuView = $this->renderView('_partials/_breadcrumb_menu.html.twig', [
            'menu' => $menu,
        ]);

        return new Response($menuView);
    }

}

