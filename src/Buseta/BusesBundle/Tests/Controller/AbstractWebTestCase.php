<?php
/**
 * Created by PhpStorm.
 * User: dundivet
 * Date: 1/04/16
 * Time: 11:00
 */

namespace Buseta\BusesBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\User;

abstract class AbstractWebTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->logIn();
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');
        $firewall = 'secured_area';
        $user = new User('admin', 'Oob0FmH8l5IkiIuMaHmbxA2SrO/6o3yiRCtIs4JS7ZWBHirzUZwxCWbSLVbGT+f3H8cqUIcta5hkWfmo/+LJgA==', array('ROLE_SUPER_ADMIN'));
        $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_ADMIN'));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
