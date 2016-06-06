<?php
/**
 * Created by PhpStorm.
 * User: kalandraka
 * Date: 6/06/16
 * Time: 3:55
 */

namespace Buseta\TransitoBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class MultaControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $path = $this->router->generate('multa');
        $this->assertEquals(
            '/multa/',
            $path,
            'The path for route "multa" is not equal to /multa/'
        );

        // Test search/list Multa
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
     *   'numArticulo' => ...,
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
        $path = $this->router->generate('multa');
        $formData = array(
            'buseta_multa_filter[descripcion]' => $data['descripcion'],
            'buseta_multa_filter[numArticulo]' => $data['numArticulo'],
            'buseta_multa_filter[vehiculo]' => $data['vehiculo'],
            'buseta_multa_filter[chofer]' => $data['chofer'],
            'buseta_multa_filter[fechaInicio]' => $data['fechaInicio'],
            'buseta_multa_filter[fechaFin]' => $data['fechaFin'],
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
        $path = $this->router->generate('multa_show', array('id' => $id));
        $this->assertEquals(
            sprintf('/multa/%d/show', $id),
            $path,
            sprintf('The path for route "multa_show" is not equal to /multa/%d/show', $id)
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
        $path = $this->router->generate('multa_edit', array('id' => $id));
        $this->assertEquals(
            sprintf('/multa/%d/edit', $id),
            $path,
            sprintf('The path for route "multa_show" is not equal to /multa/%d/edit', $id)
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
        $path = $this->router->generate('multa_new');
        $this->assertEquals(
            '/multa/new',
            $path,
            'The path for route "multa_new" is not equal to /multa/new'
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
            'a' => array(8),
            'b' => array(9),
            'c' => array(10),
            'd' => array(11),
        );
    }

    public function searchProvider()
    {
        return array(
            'a' => array(
                'data' => array(
                    'descripcion' => 'Multa descripción',
                    'numArticulo' => '',
                    'vehiculo' => '',
                    'chofer' => '',
                    'fechaInicio' => '',
                    'fechaFin' => '',
                ),
            ),
            'b' => array(
                'data' => array(
                    'descripcion' => '',
                    'numArticulo' => 2,
                    'vehiculo' => '',
                    'chofer' => '',
                    'fechaInicio' => '',
                    'fechaFin' => '',
                ),
            ),
            'c' => array(
                'data' => array(
                    'descripcion' => '',
                    'numArticulo' => '',
                    'vehiculo' => 1,
                    'chofer' => '',
                    'fechaInicio' => '',
                    'fechaFin' => '',
                ),
            ),
            'd' => array(
                'data' => array(
                    'descripcion' => '',
                    'numArticulo' => '',
                    'vehiculo' => '',
                    'chofer' => 20,
                    'fechaInicio' => '',
                    'fechaFin' => '',
                ),
            ),
            'e' => array(
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
