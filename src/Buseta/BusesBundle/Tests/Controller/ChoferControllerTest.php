<?php

namespace Buseta\BusesBundle\Tests\Controller;

class ChoferControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        // Test search/list Chofer
        $crawler = $this->client->request('GET', '/chofer/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /chofer/");

        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'new\']')->count(), 'Missing add new button');
    }

    public function testShow()
    {
        // Test search/list chofer
        $crawler = $this->client->request('GET', '/chofer/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /chofer/");

        // Check data in the index view
        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'show\']')->count(), 'Missing elements/test cases');
        $crawler = $this->client->click($crawler->filter('a[href$=\'show\']')->eq(0)->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /chofer/\\d+/show");

        // Check buttons in show view
        //$this->checkFunctionalityButtons($crawler, array('Editar', 'Volver', 'Eliminar'), 'Edit');
    }

    public function testEdit()
    {
        // Test search/list chofer
        $crawler = $this->client->request('GET', '/chofer/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /chofer/");

        // Check data in the index view
        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'edit\']')->count(), 'Missing elements/test cases');
        $crawler = $this->client->click($crawler->filter('a[href$=\'edit\']')->eq(0)->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /chofer/\\d+/edit");

        // Check buttons in edit view
        //$this->checkFunctionalityButtons($crawler, array('Salvar', 'Volver'), 'Edit');
    }

    public function testNew()
    {
        // Test create chofer
        $crawler = $this->client->request('GET', '/chofer/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /chofer/new");

        //$this->checkFunctionalityButtons($crawler, array('Salvar', 'Volver'), 'New');
    }

    private function checkFunctionalityButtons(\Symfony\Component\DomCrawler\Crawler $crawler, array $links, $view)
    {
        foreach ($links as $link) {
            $this->assertEquals(1, $crawler->selectLink($link)->count(), sprintf('Missing "%s" link on %s view', $link, $view));
        }
    }
}
