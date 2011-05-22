<?php

namespace Dsyph3r\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JobController extends Controller
{
    public function showAction($id)
    {
        $em     = $this->get('doctrine.orm.entity_manager');
        $job    = $em->getRepository('Dsyph3rJobeetBundle:Job')->findOneById($id);
        
        if (!$job) {
            throw new NotFoundHttpException('The Job does not exist.');
        }
        
        return $this->render('Dsyph3rJobeetBundle:Job:show.html.twig', array(
            'job' => $job,
        ));
        
    }
    
    public function editAction($id)
    {
        $em     = $this->get('doctrine.orm.entity_manager');
        $job    = $em->getRepository('Dsyph3rJobeetBundle:Job')->findOneById($id);
        
        if (!$job) {
            throw new NotFoundHttpException('The Job does not exist.');
        }
        
        return $this->render('Dsyph3rJobeetBundle:Job:edit.html.twig', array(
            'job' => $job,
        ));
        
    }
}
