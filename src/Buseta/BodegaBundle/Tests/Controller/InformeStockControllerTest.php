<?php

namespace Buseta\BodegaBundle\Tests\Controller;

use Buseta\BusesBundle\Tests\Controller\AbstractWebTestCase;

class InformeStockControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        // Test search/list informe_stock
        $crawler = $this->client->request('GET', '/informe_stock/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /informe_stock/");

    }

    private function checkFunctionalityButtons(\Symfony\Component\DomCrawler\Crawler $crawler, array $links, $view)
    {
        foreach ($links as $link) {
            $this->assertEquals(1, $crawler->selectLink($link)->count(), sprintf('Missing "%s" link on %s view', $link, $view));
        }
    }
}