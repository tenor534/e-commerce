<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app_')]
class CategoriesController extends AbstractController
{
    public int $itemPerPage;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->itemPerPage = $parameterBag->get('ITEM_PER_PAGE');
    }

    #[Route('/{slug}', name: 'products_list')]
    public function list(Categories $category, ProductsRepository $productsRepository, string $slug, Request $request ): Response
    {
        //liste des produits de la catégorie
        //$products = $category->getProducts();

        //Get the page number from request
        $page = $request->query->getInt('page', 1);

        $products = $productsRepository->findProductsPaginated($page, $slug, $this->itemPerPage);       

        return $this->render(
            'categories/list.html.twig', [
            'category' => $category,
            'products' => $products,            
            'pageName' => 'Products list'             
        ]);
    }
}

