<?php
//
//namespace Dsyph3r\JobeetBundle\DataFixtures\ORM;
//
//use Doctrine\Common\DataFixtures\AbstractFixture,
//    Doctrine\Common\DataFixtures\OrderedFixtureInterface,
//    Dsyph3r\JobeetBundle\Entity\Category;
//
//class CategoryFixtures extends AbstractFixture implements OrderedFixtureInterface
//{
//    public function load($em)
//    {
//        $design = new Category();
//        $design->setName('Design');
//
//        $programming = new Category();
//        $programming->setName('Programming');
//
//        $manager = new Category();
//        $manager->setName('Manager');
//
//        $administrator = new Category();
//        $administrator->setName('Administrator');
//
//        $em->persist($design);
//        $em->persist($programming);
//        $em->persist($manager);
//        $em->persist($administrator);
//
//        $em->flush();
//        
//        $this->addReference('category-design', $design);
//        $this->addReference('category-programming', $programming);
//        $this->addReference('category-manager', $administrator);
//        $this->addReference('category-adminitrator', $administrator);
//    }
//    
//    public function getOrder()
//    {
//        return 10;
//    }
//
//
//}
