<?php
/**
 * Created by PhpStorm.
 * User: dundivet
 * Date: 26/05/16
 * Time: 18:27
 */

namespace Buseta\BusesBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class DefaultController
 *
 * @package Buseta\BusesBundle\Controller
 *
 * @Route("/dashboard")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="busetabuses_dashboard")
     * @Method("GET")
     */
    public function principalAction()
    {
        return $this->render('@BusetaBuses/Default/principal.html.twig');
    }
}
