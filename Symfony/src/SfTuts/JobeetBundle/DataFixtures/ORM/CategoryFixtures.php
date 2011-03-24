<?php

// src/SfTuts/JobeetBundle/DataFixtures/ORM/CategoryFixtures.php

namespace SfTuts\JobeetBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture,
    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
    SfTuts\JobeetBundle\Entity\Category;

class CategoryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($em)
    {
        $design = new Category();
        $design->setName('Design');

        $programming = new Category();
        $programming->setName('Programming');

        $manager = new Category();
        $manager->setName('Manager');

        $administrator = new Category();
        $administrator->setName('Administrator');

        $em->persist($design);
        $em->persist($programming);
        $em->persist($manager);
        $em->persist($administrator);

        $em->flush();
        
        // store reference to categories for Job relation to Category
        $this->addReference('category-design', $design);
        $this->addReference('programming', $programming);
        $this->addReference('category-manager', $manager);
        $this->addReference('category-administrator', $administrator);

    }
    
    public function getOrder()
    {
        return 10; // number in which order to load fixtures
    }

}
