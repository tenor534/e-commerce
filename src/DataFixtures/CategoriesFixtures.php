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
        //Informatique, Tablettes
        $parent = $this->createCatetory( 
            parent:         null , 
            name:           'Informatique, Tablettes', 
            description:    'Profitez de toutes nos catégories informatique : pc portable, pc de bureau, MacBook, iMac, PC gaming, tablette tactile, Chromebook et liseuse numérique Kobo.', 
            categoryOrder:  '0', 
            photo:          'informatique.jpg', 
            manager:        $manager
        );
        $this->createCatetory(
            $parent,
            'PC portables ', 
            'Découvrez nos gammes de PC portables selon vos besoins : PC Ultraportable ou Chromebook si vous avez besoin d\'un ordinateur performant mais peu encombrant, PC Portable Gamer si vous êtes un accro des jeux.', 
            1, 
            'portable.jpg', 
            $manager
        );
        $this->createCatetory(
            $parent,
            'Ordinateurs de bureau', 
            ' Asus, Acer, HP, Lenovo ou encore Dell. Retrouvez également nos ordinateurs Apple : iMac et MacBook ainsi que nos PC Hybride 2 en 1 et nos ordinateurs portables.', 
            1, 
            'desktop.jpg', 
            $manager
        );
        $this->createCatetory(
            $parent,
            'Imprimante, scanner', 
            'Vous hésitez entre une imprimante à jet d’encre, imprimante multifonction, imprimante laser, imprimante 3D ou une imprimante pas cher ? Quoi que vous décidiez, elles sont toutes à la Fnac ! Vous avez le choix parmi les plus grandes marques : Canon, HP, Epson, Brother, Samsung et bien d’autres.', 
            1, 
            'imprimante.jpg', 
            $manager
        );
        $this->createCatetory(
            $parent,
            'Toutes les tablettes', 
            'Découvrez notre sélection de tablettes tactiles parmi les plus grandes marques : Samsung Galaxy Tab, tablette Lenovo, tablette Huawei.', 
            1, 
            'tablette.jpg', 
            $manager
        );

        //Mode
        $parent = $this->createCatetory( 
            parent:         null , 
            name:           'Mode', 
            description:    'bla bla', 
            categoryOrder:  '0', 
            photo:          'mode.jpg', 
            manager:        $manager
        );
        $this->createCatetory(
            $parent,
            'Homme', 
            'Vêtements, Sous-Vêtements, Chaussures, Accessoires, Grandes Tailles, Nos Collections, Marques', 
            1, 
            'home.jpg', 
            $manager
        );
        $this->createCatetory(
            $parent,
            'Femme', 
            'Vêtements, Sous-Vêtements, Chaussures, Accessoires, Grandes Tailles, Nos Collections, Marques', 
            1, 
            'femme.jpg', 
            $manager
        );
        $this->createCatetory(
            $parent,
            'Maison', 
            'Linge de maison, Décoration, Jardin, Animalerie', 
            1, 
            'maison.jpg', 
            $manager
        );

        $this->createCatetory(
            $parent,
            'Loisir', 
            'Sports d\'hiver, Marche & Randonnée, Camping, Vélo, Jeux, Coffrets cadeaux', 
            1, 
            'loisir.jpg', 
            $manager
        );

        $manager->flush();                
    }

    public function createCatetory(
        Categories  $parent = null,
        string      $name,         
        string      $description,         
        int         $categoryOrder,         
        string      $photo,         

        ObjectManager $manager
        ){

        $category = new Categories();

        $category->setParent($parent);
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());

        $category->setDescription($description);
        $category->setCategoryOrder($categoryOrder);
        $category->setPhoto($photo);
        
        $manager->persist($category);

        $this->addReference( 'cat-'. $this->counter, $category);
        $this->counter++;

        return $category;
    }
}
