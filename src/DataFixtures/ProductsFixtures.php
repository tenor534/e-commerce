<?php

namespace App\DataFixtures;

use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class ProductsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        //Use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create('fr_FR');

        for($prod = 1; $prod <= 100; $prod++){
            $product = new Products();
            $product->setName($faker->text(10));
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(900, 150000));
            $product->setStock($faker->numberBetween(0, 10));

            //Cherche une reférence d'une catégorie au hasard
            $category = $this->getReference('cat-'. rand(1, 10));
            
            $product->setCategories($category);
            
            $this->setReference('prod-'.$prod, $product);

            //$manager->persist($product);
           
        }

        //$manager->flush();
    }
}
