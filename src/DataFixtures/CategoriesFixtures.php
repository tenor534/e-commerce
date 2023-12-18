<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;



class CategoriesFixtures extends Fixture
{

    private $counter = 1;
    public function __construct(private SluggerInterface $slugger)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCatetory( name: 'Informatique', parent: null ,manager: $manager);
        $this->createCatetory('Ordinateurs portables', $parent, $manager);
        $this->createCatetory( 'Ecran', $parent, $manager);
        $this->createCatetory( 'Souris', $parent, $manager);
        $this->createCatetory( 'Claviers', $parent, $manager);

        $parent = $this->createCatetory( name: 'Mode', parent: null ,manager: $manager);
        $this->createCatetory('Homme', $parent, $manager);
        $this->createCatetory( 'Femme', $parent, $manager);
        $this->createCatetory( 'Enfant', $parent, $manager);
        $this->createCatetory( 'Mixte', $parent, $manager);

        $manager->flush();  
        
        /*
        $parent = new Categories();
        $parent->setName('Informatique');
        $parent->setSlug($this->slugger->slug($parent->getName())->lower());
        $manager->persist($parent);

        $category = new Categories();
        $category->setName('Ordinateur');
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        $category = new Categories();
        $category->setName('Ecran');
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        $manager->flush();
        */
    }

    public function createCatetory(
        string $name, 
        Categories $parent = null,
        ObjectManager $manager
        ){

        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        $this->addReference( 'cat-'. $this->counter, $category);
        $this->counter++;

        return $category;
    }
}
