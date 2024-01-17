<?php

namespace App\Controller;

use App\Entity\Users;
use App\Security\AppCustomAuthenticator;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class GoogleController extends AbstractController
{

    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        return $clientRegistry
            ->getClient('google')
            ->redirect([], [
                'profile', 'email'
            ]);
    }


    /**
     * After going to Google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     */
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(
        Request $request,
        ClientRegistry $clientRegistry,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $userAuthenticator,
        UsersAuthenticator $authenticator
    ) {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_main');
        }

        $client = $clientRegistry->getClient('google');

        try {
            $googleUser = $client->fetchUser();           

            // check if email exist
            $existingUser = $entityManager->getRepository(Users::class)
                ->findOneBy(['email' => $googleUser->getEmail()]);
            if ($existingUser) {
                return $userAuthenticator->authenticateUser(
                    $existingUser,
                    $authenticator,
                    $request
                );
            }

            $user = new Users();

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $googleUser->getId()
                )
            );                       
            
            $user->setRoles(['ROLE_USER']);
            $user->setEmail($googleUser->getEmail());
            $user->setFirstname($googleUser->getFirstName());
            $user->setLastname($googleUser->getLastName());           
            
            $user->setGoogleId($googleUser->getId());
            $user->setHostedDomain( $googleUser->getHostedDomain()?:'');

            $user->setIsVerified(true);


            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        } catch (IdentityProviderException $e) {
            var_dump($e->getMessage());
            die;
        }
    }
}
