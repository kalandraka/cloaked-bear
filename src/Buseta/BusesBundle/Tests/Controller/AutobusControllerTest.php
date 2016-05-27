<?php

namespace Buseta\BusesBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class AutobusControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $path = $this->router->generate('busetabuses_autobus');
        $this->assertEquals(
            '/vehiculo/autobus/',
            $path,
            'The path for route "busetabuses_dashboard" is not equal to /vehiculo/dashboard'
        );

        // Test search/list Autobus
        $crawler = $this->client->request('GET', $path);
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );

        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'new\']')->count(), 'Missing add new button');
    }

    /**
     * @dataProvider provider
     */
    public function testShow($id)
    {
        $path = $this->router->generate('busetabuses_autobus_show', array('id' => $id));
        $this->assertEquals(
            sprintf('/vehiculo/autobus/%d/show', $id),
            $path,
            sprintf('The path for route "busetabuses_autobus_show" is not equal to /vehiculo/autobus/%d/show', $id)
        );

        // Test search/list Autobus
        $this->client->request('GET', $path);
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );

        // Check buttons in show view
        //$this->checkFunctionalityButtons($crawler, array('Editar', 'Volver', 'Eliminar'), 'Edit');
    }

    /**
     * @dataProvider provider
     */
    public function testEdit($id)
    {
        $path = $this->router->generate('busetabuses_autobus_edit', array('id' => $id));
        $this->assertEquals(
            sprintf('/vehiculo/autobus/%d/edit', $id),
            $path,
            sprintf('The path for route "busetabuses_autobus_edit" is not equal to /vehiculo/autobus/%d/edit', $id)
        );

        $this->client->request('GET', $path);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );

        // Check buttons in edit view
        //$this->checkFunctionalityButtons($crawler, array('Salvar', 'Volver'), 'Edit');
    }

    public function testNew()
    {
        $path = $this->router->generate('busetabuses_autobus_new');
        $this->assertEquals(
            '/vehiculo/autobus/new',
            $path,
            'The path for route "busetabuses_autobus_new" is not equal to /vehiculo/dashboard/new'
        );

        $this->client->request('GET', $path);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );

        //$this->checkFunctionalityButtons($crawler, array('Salvar', 'Volver'), 'New');
    }

    private function checkFunctionalityButtons(\Symfony\Component\DomCrawler\Crawler $crawler, array $links, $view)
    {
        foreach ($links as $link) {
            $this->assertEquals(1, $crawler->selectLink($link)->count(), sprintf('Missing "%s" link on %s view', $link, $view));
        }
    }

    public function provider()
    {
        return array(
            'a' => array(1),
            'b' => array(2),
            'c' => array(3),
            'd' => array(4),
        );
    }
}
