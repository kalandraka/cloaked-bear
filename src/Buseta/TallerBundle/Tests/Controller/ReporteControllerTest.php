<?php

namespace Buseta\BusesBundle\Tests\Controller;

use Buseta\BusesBundle\Tests\Controller\AbstractWebTestCase;

class ReporteControllerTest extends AbstractWebTestCase
{
    public function testBOIndex()
    {
        // Test search/list Solicitud BO
        $crawler = $this->client->request('GET', '/reporte/index?status=BO');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/index?status=BO");

        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'new?status=BO\']')->count(), 'Missing add new button');
    }

    public function testPRIndex()
    {
        // Test search/list Solicitud PR
        $crawler = $this->client->request('GET', '/reporte/index?status=PR');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/index?status=PR");

    }

    public function testCOIndex()
    {
        // Test search/list Solicitud CO
        $crawler = $this->client->request('GET', '/reporte/index?status=CO');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/index?status=CO");

    }

    public function testBOShow()
    {
        // Test search/list Reporte
        $crawler = $this->client->request('GET', '/reporte/index?status=BO');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/index?status=BO");

        // Check data in the index view
        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'/show?status=BO\']')->count(), 'Missing elements/test cases');
        $crawler = $this->client->click($crawler->filter('a[href$=\'/show?status=BO\']')->eq(0)->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/\\d+//show?status=BO");

        // Check buttons in show view
        //$this->checkFunctionalityButtons($crawler, array('Editar', 'Volver', 'Eliminar'), 'Edit');
    }

    public function testPRShow()
    {
        // Test search/list Reporte
        $crawler = $this->client->request('GET', '/reporte/index?status=PR');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/index?status=PR");

        // Check data in the index view
        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'/show?status=PR\']')->count(), 'Missing elements/test cases');
        $crawler = $this->client->click($crawler->filter('a[href$=\'/show?status=PR\']')->eq(0)->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/\\d+//show?status=PR");

        // Check buttons in show view
        //$this->checkFunctionalityButtons($crawler, array('Editar', 'Volver', 'Eliminar'), 'Edit');
    }

    public function testCOShow()
    {
        // Test search/list Reporte
        $crawler = $this->client->request('GET', '/reporte/index?status=CO');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/index?status=CO");

        // Check data in the index view
        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'/show?status=CO\']')->count(), 'Missing elements/test cases');
        $crawler = $this->client->click($crawler->filter('a[href$=\'/show?status=CO\']')->eq(0)->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/\\d+//show?status=CO");

        // Check buttons in show view
        //$this->checkFunctionalityButtons($crawler, array('Editar', 'Volver', 'Eliminar'), 'Edit');
    }

    public function testNew()
    {
        // Test create Reporte
        $crawler = $this->client->request('GET', '/reporte/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /reporte/new");

        //$this->checkFunctionalityButtons($crawler, array('Salvar', 'Volver'), 'New');
    }

    private function checkFunctionalityButtons(\Symfony\Component\DomCrawler\Crawler $crawler, array $links, $view)
    {
        foreach ($links as $link) {
            $this->assertEquals(1, $crawler->selectLink($link)->count(), sprintf('Missing "%s" link on %s view', $link, $view));
        }
    }
}
