<?php
/**
 * Created by PhpStorm.
 * User: kalandraka
 * Date: 6/06/16
 * Time: 3:55
 */

namespace Buseta\TransitoBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class PenalAccidenteControllerTest extends AbstractWebTestCase
{
    /**
     * @dataProvider provider
     */
    public function testEdit($id)
    {
        $path = $this->router->generate('penal_accidente_edit', array('id' => $id));
        $this->assertEquals(
            sprintf('/penal_accidente/%d/edit', $id),
            $path,
            sprintf('The path for route "penal_accidente_edit" is not equal to /penal_accidente/%d/edit', $id)
        );

        $this->client->request('GET', $path);
        $this->assertEquals(
            202,
            $this->client->getResponse()->getStatusCode(),
            sprintf("Unexpected HTTP status code for %s %s", 'GET', $path)
        );
    }

    /**
     * @dataProvider accidenteProvider
     */
    public function testNew($accidenteId)
    {
        $path = $this->router->generate('penal_accidente_new', array('accidente' => $accidenteId));
        $this->assertEquals(
            sprintf('/penal_accidente/new/%d', $accidenteId),
            $path,
            sprintf('The path for route "penal_accidente_new" is not equal to /penal_accidente/new/%d', $accidenteId)
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
            'a' => array(1),
        );
    }

    public function accidenteProvider()
    {
        return array(
            'a' => array(6),
            'b' => array(7),
        );
    }

}
