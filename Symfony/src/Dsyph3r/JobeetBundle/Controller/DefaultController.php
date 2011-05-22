<?php

namespace Dsyph3r\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em     = $this->get('doctrine.orm.entity_manager');
        $jobs   = $em->getRepository('Dsyph3rJobeetBundle:Job')->findAll();

        return $this->render('Dsyph3rJobeetBundle:Default:index.html.twig', array(
            'jobs' => $jobs,
        ));        
    }
}
