<?php
/**
 * Created by PhpStorm.
 * User: kalandraka
 * Date: 6/06/16
 * Time: 3:55
 */

namespace Buseta\TransitoBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class AccidenteControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $path = $this->router->generate('accidente');
        $this->assertEquals(
            '/accidente/',
            $path,
            'The path for route "accidente" is not equal to /accidente/'
        );

        // Test search/list accidente
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
     *   'descripcion' => ...,
     *   'vehiculo' => ...,
     *   'chofer' => ...,
     *   'fechaInicio' => ...,
     *   'fechaFin' => ...,
     * )
     *
     * @dataProvider searchProvider
     *
     * @depends      testIndex
     */
    public function testSearch($data)
    {
        $path = $this->router->generate('accidente');
        $formData = array(
            'buseta_accidente_filter[descripcion]' => $data['descripcion'],
            'buseta_accidente_filter[vehiculo]' => $data['vehiculo'],
            'buseta_accidente_filter[chofer]' => $data['chofer'],
            'buseta_accidente_filter[fechaInicio]' => $data['fechaInicio'],
            'buseta_accidente_filter[fechaFin]' => $data['fechaFin'],
        );

        $crawler = $this->client->request('GET', $path);
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
        $path = $this->router->generate('accidente_show', array('id' => $id));
        $this->assertEquals(
            sprintf('/accidente/%d/show', $id),
            $path,
            sprintf('The path for route "accidente_show" is not equal to /accidente/%d/show', $id)
        );
        // Test search/list Multa
        $this->client->request('GET', $path);
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );
    }

    /**
     * @dataProvider provider
     */
    public function testEdit($id)
    {
        $path = $this->router->generate('accidente_edit', array('id' => $id));
        $this->assertEquals(
            sprintf('/accidente/%d/edit', $id),
            $path,
            sprintf('The path for route "accidente_edit" is not equal to /accidente/%d/edit', $id)
        );

        $this->client->request('GET', $path);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );
    }

    public function testNew()
    {
        $path = $this->router->generate('accidente_new');
        $this->assertEquals(
            '/accidente/new',
            $path,
            'The path for route "accidente_new" is not equal to /accidente/new'
        );

        $this->client->request('GET', $path);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );
    }

    public function provider()
    {
        return array(
            'a' => array(4),
            'b' => array(6),
            'c' => array(7),
            'd' => array(8),
        );
    }

    public function searchProvider()
    {
        return array(
            'a' => array(
                'data' => array(
                    'descripcion' => 'Accidente descripción',
                    'vehiculo' => '',
                    'chofer' => '',
                    'fechaInicio' => '',
                    'fechaFin' => '',
                ),
            ),
            'b' => array(
                'data' => array(
                    'descripcion' => '',
                    'vehiculo' => 1,
                    'chofer' => '',
                    'fechaInicio' => '',
                    'fechaFin' => '',
                ),
            ),
            'c' => array(
                'data' => array(
                    'descripcion' => '',
                    'vehiculo' => '',
                    'chofer' => 20,
                    'fechaInicio' => '',
                    'fechaFin' => '',
                ),
            ),
            'd' => array(
                'data' => array(
                    'descripcion' => '',
                    'numArticulo' => '',
                    'vehiculo' => '',
                    'chofer' => '',
                    'fechaInicio' => '01/03/2016',
                    'fechaFin' => '31/08/2016',
                ),
            ),
        );
    }
}
