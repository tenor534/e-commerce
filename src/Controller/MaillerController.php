<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mailler', name: 'app_mailler_')]
class MaillerController extends AbstractController
{
    #[Route('/send-email', name: 'send')]
    public function index(MailerInterface $mailer): Response
    {
        $email= (new Email())
            ->from('sample-sender@binaryboxtuts.com')
            ->to('sh.rakotondrabe@it-students.fr')
            ->subject('Email test : do not repply')
            ->text('A simple email using mailtrap. ok');

        $mailer->send($email);

        $this->addFlash('message', 'Email a été envoyé avec succès');
        return $this->redirectToRoute('app_main');
    }
}
