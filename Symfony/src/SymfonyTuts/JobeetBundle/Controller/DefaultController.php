<?php

namespace SymfonyTuts\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SymfonyTutsJobeetBundle:Default:index.html.twig');
    }
}
