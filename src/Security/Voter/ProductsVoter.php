<?php

namespace App\Security\Voter;

use App\Entity\Products;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsVoter extends Voter
{
    const EDIT      = 'PRODUCT_EDIT';
    const DELETE    = 'PRODUCT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;

    }

    public function supports(string $attribute, $product): bool       
    {
        if(! in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        }
        if(! $product instanceof Products){
            return false;
        }

        //return in_array($attribute, [self::EDIT, self::DELETE]) && $product instanceof Products;

        return true;
        
    }

    protected function voteOnAttribute($attribute, $product, TokenInterface $token): bool
    {
        //On récupère l'utilisateur à partir du token
        $user = $token->getUser();

        //? utilisateur est connecté, sinon on ne va pas plus loin
        if(!$user instanceof UserInterface){
            return false;
        }

        //? utilisateur est ADMIN
        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }

        //? les permissions : il peut (edit/delete) si il a le role PRODUCT_ADMIN
        switch($attribute){
            case self::EDIT:
                //? user can edit                
                return $this->canEdit();
                break;
            case self::DELETE:
                //? user can delete
                return $this->canDelete();
                break;
        }
    }

    private function canEdit(){
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }

    private function canDelete(){
        return $this->security->isGranted('ROLE_ADMIN');
    }
}