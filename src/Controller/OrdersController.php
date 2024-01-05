<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/orders', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/add', name: 'add')]
    public function add(SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $cart = $session->get('cart', []);

        if($cart === []){
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('app_main');
        }

        //Le panier n'est pas vide, on crée la commande
        $order = new Orders();

        // On remplit la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());

        // On parcourt le panier pour créer les détails de commande
        foreach($cart as $item => $quantity){
            $orderDetails = new OrdersDetails();

            // On va chercher le produit
            $product = $productsRepository->find($item);
            
            $price = $product->getPrice();

            // On crée le détail de commande
            $orderDetails->setProducts($product);
            $orderDetails->setPrice($price);
            $orderDetails->setQuantity($quantity);

            $order->addOrdersDetail($orderDetails);
        }

        // On persiste et on flush
        $em->persist($order);
        $em->flush();

        $session->remove('cart');

        $this->addFlash('message', 'Commande créée avec succès');
        return $this->redirectToRoute('app_main');
    }


    #[Route('/show', name: 'show', methods: ['GET'])]
    public function show(OrdersRepository $ordersRepository): Response
    {
        $orders = $ordersRepository->findBy(
            [],
            ['created_at' => 'asc']
        );
       
        return $this->render('orders/show.html.twig', [
            'orders'    => $orders,
            'pageName'  => 'Mes commandes'
        ]);
    }

    #[Route('/show/details/{id}', name: 'show_details', methods: ['GET'])]
    public function showDetails(Orders $order): Response
    {

        //Détail de la commande
        $details = $order->getOrdersDetails();        

        /**
         * 
         * TOTAL  
         * Montant du panier
         * 108,99€
         * Frais d'envoi
         * 4,95€
         * Sous-total 
         * 113,94€
        */
        
        $amount     = 0; // Total
        $shipping   = 499; // val * 100

        foreach($details as $detail){
            $amount += ($detail->getPrice() * $detail->getQuantity());
        }

        $total = $amount + $shipping;
        
        return $this->render('orders/details.html.twig', [
            'order'     => $order,
            'details'   => $details, 
            'amount'    => $amount,
            'shipping'  => $shipping,
            'total'     => $total,
            'user'      => $this->getUser(),
            'pageName'  => 'Détail de la commande'
        ]);
    }

    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(Orders $order, EntityManagerInterface $entityManager)
    {
        //Etat de la commande : En cours (0) - Expédiées (1) - Retournées (2) - Annulées (3)
        
        $order->setStatus(3);
        $entityManager->persist($order);
        $entityManager->flush();
        
        //Affiche un message flash
        $this->addFlash('success', 'Votre commande a été annulée ! Merci.');

        //On redirige vers la page liste des commandes
        return $this->redirectToRoute('app_orders_show');
    }
}
