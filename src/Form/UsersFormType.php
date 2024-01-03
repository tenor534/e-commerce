<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder         
     
            /*->add('roles', CollectionType::class, [
                'entry_type'   => ChoiceType::class,
                'entry_options'  => [
                    'choices' => [
                        'Administrator' => 'ROLE_ADMIN',
                        'Admin Products' => 'ROLE_PRODUCT_ADMIN',
                        'Member' => 'ROLE_USER',
                    ],
                    'choice_attr' => [
                        'Administrator' => ['data-color' => 'Red'],
                        'Admin Products' => ['data-color' => 'Yellow'],
                        'Member' => ['data-color' => 'Green'],
                    ],
                ],
            ])*/    

            ->add('email'    , EmailType::class, [
                'label'     => 'Email : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])  
            /*->add('password'    , PasswordType::class, [
                'label'     => 'Password : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])*/            
            ->add('lastname'    , TextType::class, [
                'label'     => 'Lastname : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])                     
            ->add('firstname'    , TextType::class, [
                'label'     => 'Firstname : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])                               

            ->add('address'    , TextType::class, [
                'label'     => 'Adress : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])
            ->add('zipcode'    , TextType::class, [
                'label'     => 'Zip Code : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])
            ->add('city'    , TextType::class, [
                'label'     => 'City : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])
            
            //->add('is_verified')
            
            /*->add('created_at', TypeDateType::class, [
                'widget' => 'single_text',
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',
                'label'     => 'Created at : ',
                'required'  => true,
                'attr'      => [
                        'class' => 'form-control'
                ]
            ])*/
            ;        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
