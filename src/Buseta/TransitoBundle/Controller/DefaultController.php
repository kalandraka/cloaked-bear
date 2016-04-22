<?php

namespace Buseta\TransitoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BusetaTransitoBundle:Default:index.html.twig', array('name' => $name));
    }
}
