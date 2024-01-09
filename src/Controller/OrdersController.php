<?php

namespace App\Controller;

use App\Entity\Invoices;
use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Service\PicturesService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;

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

        //On crée une première facture
        $invoice = new Invoices();

        $invoice->setReference('FACT-'.uniqid());
        $invoice->setShipping(499); // 4.99
        //$invoice->setTrackingNumber('');
        //$invoice->setShippedAt('');
        $invoice->setOrders($order);        

        // On persiste et on flush
        $em->persist($invoice);
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

    /*
    #[Route('/generate/invoice/{id}', name: 'generate_invoice', methods: ['GET'])]
    public function generateInvoicePdf(Invoices $invoice, Pdf $knpSnappyPdf, Request $request): PdfResponse
    {
        //Gégeration d'une facture PDF
        //dd($invoice);
        $options = [
            'margin-top'    => 2,
            'margin-bottom' => 2,
            'margin-left'   => 2,
            'margin-right'  => 3,
        ];

        //La commande
        $order      = $invoice->getOrders();
        $details    = $order->getOrdersDetails();

        $amount     = 0; // Total
        $shipping   = 499; // val * 100

        foreach($details as $detail){
            $amount += ($detail->getPrice() * $detail->getQuantity());
        }

        $total = $amount + $shipping;

        $html = $this->renderView('invoices/pdf/invoice.html.twig', array(
            'invoice'  => $invoice,
            'order'    => $order,
            'details'  => $details,
            'amount'   => $amount,
            'shipping' => $shipping,
            'total'    => $total,
            'user'     => $this->getUser(), 
            'public_directory'  => $this->getParameter('public_directory')
            
        ));      
        
        $knpSnappyPdf->setOptions($options);

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml(
                $html,
                array('orientation' => 'Portrait')
            ),
            'file.pdf'
        );
    }
    */


    #[Route('/generate/invoice/{id}', name: 'generate_invoice', methods: ['GET'])]
    public function generateInvoicePdf(Invoices $invoice, PicturesService $picturesService ): PdfResponse    
    {
        //La commande
        $order      = $invoice->getOrders();
        $details    = $order->getOrdersDetails();

        $amount     = 0; // Total
        $shipping   = 499; // val * 100

        foreach($details as $detail){
            $amount += ($detail->getPrice() * $detail->getQuantity());
        }

        $total = $amount + $shipping;

        $data = [
            'logoSrc'  => $picturesService->imageToBase64($this->getParameter('kernel.project_dir') . '/public/assets/img/logo-534-512x512.webp'),
            'imagePath'=> $this->getParameter('kernel.project_dir') . '/public/assets/uploads/products/images/mini/300x300-',            
            'cssPath'  => $this->getParameter('kernel.project_dir') . '/public/assets/css',            
            'invoice'  => $invoice,
            'order'    => $order,
            'details'  => $details,
            'amount'   => $amount,
            'shipping' => $shipping,
            'total'    => $total,
            'user'     => $this->getUser(), 
            'pictureService' => $picturesService
        ];      
        
        $html   =  $this->renderView('invoices/pdf/invoice.html.twig', $data);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
         
        return new PdfResponse(
            $dompdf->stream('resume', [                
                'orientation'   => 'Portrait',
                'page-size'     => 'A4',
                'encoding'      => 'UTF-8',
            ]),
            'invoice.pdf',
        );
    }       
}
