<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email'       , EmailType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'aria-describedby'  =>  'emailHelp'                    
                ],
                'label' => 'E-mail',
                'label_attr' => [
                    'class' => 'form-label', 
                    'for'=>"email"
                ],
            ])
            ->add('lastname'    , TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('firstname'   , TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('address'      , TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('zipcode'     , TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('city'        , TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
         
            ->add('RGPDConsent', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],                
                'label' => 'J\'accepte les termes et conditions RGPD',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete'  => 'new-password',
                    'class'         => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],                
                'label' => 'Password'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
