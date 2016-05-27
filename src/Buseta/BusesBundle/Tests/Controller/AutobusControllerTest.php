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
     * array(
     *   'matricula' => ...,
     *   'numero' => ...,
     *   'marca' => ...,
     *   'estilo' => ...,
     *   'color' => ...,
     *   'marcaMotor' => ...,
     * )
     *
     * @dataProvider searchProvider
     *
     * @depends testIndex
     */
    public function testSearch($data)
    {
        $path = $this->router->generate('busetabuses_autobus');
        $formData = array(
            'busetabuses_autobus_filter[matricula]' => $data['matricula'],
            'busetabuses_autobus_filter[numero]' => $data['numero'],
            'busetabuses_autobus_filter[marca]' => $data['marca'],
            'busetabuses_autobus_filter[estilo]' => $data['estilo'],
            'busetabuses_autobus_filter[color]' => $data['color'],
            'busetabuses_autobus_filter[marca_motor]' => $data['marcaMotor'],
        );

        $crawler = $this->client->request('GET', $path);
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s.", 'GET', $path)
        );
        $form = $crawler->selectButton('Filtrar')->form($formData);
        $this->client->submit($form);

        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s after form submission.", 'GET', $path)
        );
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

    public function searchProvider()
    {
        return array(
            'a' => array(
                'data' => array(
                    'matricula' => 'SJB 11661',
                    'numero' => '',
                    'marca' => '',
                    'estilo' => '',
                    'color' => '',
                    'marcaMotor'=> ''
                )
            ),
            'b' => array(
                'data' => array(
                    'matricula' => '',
                    'numero' => '1',
                    'marca' => '',
                    'estilo' => '',
                    'color' => '',
                    'marcaMotor'=> ''
                )
            ),
            'c' => array(
                'data' => array(
                    'matricula' => '',
                    'numero' => '',
                    'marca' => 1,
                    'estilo' => '',
                    'color' => '',
                    'marcaMotor'=> ''
                )
            ),
            'd' => array(
                'data' => array(
                    'matricula' => '',
                    'numero' => '',
                    'marca' => '',
                    'estilo' => 1,
                    'color' => 1,
                    'marcaMotor'=> ''
                )
            ),
            'e' => array(
                'data' => array(
                    'matricula' => '',
                    'numero' => '',
                    'marca' => '',
                    'estilo' => '',
                    'color' => '',
                    'marcaMotor'=> 1
                )
            ),
        );
    }
}
