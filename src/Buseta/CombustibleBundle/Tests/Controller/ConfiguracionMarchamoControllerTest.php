<?php

namespace Buseta\BodegaBundle\Tests\Controller;

use Buseta\BusesBundle\Tests\Controller\AbstractWebTestCase;

class ConfiguracionMarchamoControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        // Test search/list ConfiguracionMarchamo
        $crawler = $this->client->request('GET', '/configuracion/marchamo/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /configuracion/marchamo/");

        $this->assertGreaterThan(0, $crawler->filter('a[href$=\'new\']')->count(), 'Missing add new button');
    }


    private function checkFunctionalityButtons(\Symfony\Component\DomCrawler\Crawler $crawler, array $links, $view)
    {
        foreach ($links as $link) {
            $this->assertEquals(1, $crawler->selectLink($link)->count(), sprintf('Missing "%s" link on %s view', $link, $view));
        }
    }
}