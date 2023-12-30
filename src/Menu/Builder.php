<?php

namespace App\Menu;

use App\Entity\Blog;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options): ItemInterface
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', ['route' => 'homepage']);

        // access services from the container!
        $em = $this->container->get('doctrine')->getManager();
        // findMostRecent and Blog are just imaginary examples
        $blog = $em->getRepository(Blog::class)->findMostRecent();

        $menu->addChild('Latest Blog Post', [
            'route' => 'blog_show',
            'routeParameters' => ['id' => $blog->getId()]
        ]);

        // create another menu item
        $menu->addChild('About Me', ['route' => 'about']);
        // you can also add sub levels to your menus as follows
        $menu['About Me']->addChild('Edit profile', ['route' => 'edit_profile']);

        // ... add more children

        return $menu;
    }
}