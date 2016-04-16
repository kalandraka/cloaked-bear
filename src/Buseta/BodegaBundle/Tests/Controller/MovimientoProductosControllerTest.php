<?php

namespace Buseta\BodegaBundle\Tests\Controller;

use Buseta\BusesBundle\Tests\Controller\AbstractWebTestCase;

class MovimientoProductosControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        // Test search/list Movimiento
        $crawler = $this->client->request('GET', '/bodega/movimiento/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /bodega/movimiento/");

        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'new\']')->count(), 'Missing add new button');
    }

    public function testShow()
    {
        // Test search/list Movimiento
        $crawler = $this->client->request('GET', '/bodega/movimiento/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /bodega/movimiento/");

        // Check data in the index view
        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'show\']')->count(), 'Missing elements/test cases');
        $crawler = $this->client->click($crawler->filter('a[href$=\'show\']')->eq(0)->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /bodega/movimiento/\\d+/show");

        // Check buttons in show view
        //$this->checkFunctionalityButtons($crawler, array('Editar', 'Volver', 'Eliminar'), 'Edit');
    }

    public function testEdit()
    {
        // Test search/list Movimiento
        $crawler = $this->client->request('GET', '/bodega/movimiento/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /bodega/movimiento/");

        // Check data in the index view
        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'edit\']')->count(), 'Missing elements/test cases');
        $crawler = $this->client->click($crawler->filter('a[href$=\'edit\']')->eq(0)->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /bodega/movimiento/\\d+/edit");

        // Check buttons in edit view
        //$this->checkFunctionalityButtons($crawler, array('Salvar', 'Volver'), 'Edit');
    }

    public function testNew()
    {
        // Test create Movimiento
        $crawler = $this->client->request('GET', '/bodega/movimiento/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /bodega/movimiento/new");

        //$this->checkFunctionalityButtons($crawler, array('Salvar', 'Volver'), 'New');
    }

    private function checkFunctionalityButtons(\Symfony\Component\DomCrawler\Crawler $crawler, array $links, $view)
    {
        foreach ($links as $link) {
            $this->assertEquals(1, $crawler->selectLink($link)->count(), sprintf('Missing "%s" link on %s view', $link, $view));
        }
    }
}