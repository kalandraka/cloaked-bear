<?php

namespace Buseta\BusesBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultControllerTest
 *
 * @package Buseta\BusesBundle\Tests\Controller
 */
class DefaultControllerTest extends AbstractWebTestCase
{
    public function testPrincipal()
    {
        $router = $this->client->getContainer()->get('router');
        $path = $router->generate('busetabuses_dashboard', array());

        $this->assertEquals(
            '/vehiculo/dashboard/',
            $path,
            'The path for route "busetabuses_dashboard" is not equal to /vehiculo/dashboard'
        );

        $this->client->request('GET', $path);
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /vehiculo/dashboard"
        );
    }
}
