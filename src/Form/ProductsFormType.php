<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Images;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Positive;

class ProductsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('name'    , TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])                     
            ->add('description'    , TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price'    , MoneyType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'divisor'=> 100,
                'constraints' => [
                    new Positive(
                        message: 'Le prix ne peux être négatif'
                    )
                ]
            ]) 
            ->add('stock'    , IntegerType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            
            //->add('created_at')            
            //->add('slug')

            // Brochure du produit
            ->add('brochure', FileType::class, [
                'label' => 'Brochure (PDF file)',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],
            ])

            //All car images multiples    
            ->add('images', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' =>false,
                'required' => false,
                'constraints' => [
                    new All(
                        new Image([
                            'maxWidth' => 1280,
                            'maxWidthMessage' => 'L\' image doit faire 1280 pix de large maximum' 
                        ])
                    )                
                ] 
            ])


            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control'
                ],
                'group_by' => 'parent.name',
                'query_builder' => function(CategoriesRepository $cr){
                    return $cr->createQueryBuilder('c')
                    ->where('c.parent IS NOT NULL')                    
                    ->orderBy('c.name', 'ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
