<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Tests\Controller;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Ant\Bundle\ApiSocialBundle\Controller\UserController;

/**
 * Class UserControllerTest
 *
 * @package Ant\Bundle\ApiSocialBundle\Tests\Controller
 */
class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    private $mockUserManager;
    private $containerMock;
    private $templatingMock;
    private $pagerMock;
    private $container;
    private $userController;

    protected function setUp()
    {
        parent::setUp();

        $this->containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockUserManager =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\UserManager')
                ->disableOriginalConstructor()
                ->getMock();
        $this->pagerMock =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Api\Util\Pager')
                ->disableOriginalConstructor()
                ->getMock();


        $this->templatingMock =
            $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
                ->disableOriginalConstructor()
                ->getMock();

        $this->container = new ContainerBuilder();
        $this->container->set('api_users',$this->mockUserManager);
        $this->container->set('templating',$this->templatingMock);
        $this->container->setParameter('users.language','en');

        $this->userController = new UserController();
        $this->userController->setContainer($this->container);
    }

    public function testUsersAction()
    {
        $request = new Request();
        $this->container->set('request',$request);
        $this->container->setParameter('users_orders', array('lastLogin' => 'desc', 'hasProfilePhoto' => 'desc'));

        $this->mockUserManager->expects($this->once())
            ->method('findAll')
            ->with(1, array('language' => 'en'), null, array('lastLogin' => 'desc', 'hasProfilePhoto' => 'desc'))
            ->willReturn($this->pagerMock);

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->usersAction($request,1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUsersActionWithNoOrderConfigured()
    {
        $request = new Request();
        $this->container->set('request',$request);
        $this->container->setParameter('users_orders', null);

        $this->mockUserManager->expects($this->once())
            ->method('findAll')
            ->with(1, array('language' => 'en'), null, null)
            ->willReturn($this->pagerMock);

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->usersAction($request,1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testUsersActionSearchAction()
    {
        $request = new Request();
        $request->query->set('search','search_value');
        $this->container->set('request',$request);
        $this->container->setParameter('users_orders', array('lastLogin' => 'desc', 'hasProfilePhoto' => 'desc'));

        $this->mockUserManager->expects($this->once())
            ->method('searchUserByNamePaginated')
            ->with('search_value', 1, null, null, array('lastLogin' => 'desc', 'hasProfilePhoto' => 'desc'))
            ->willReturn($this->pagerMock);

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->usersAction($request,1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testUsersActionSearchActionWithNoOrderConfigured()
    {
        $request = new Request();
        $request->query->set('search','search_value');
        $this->container->set('request',$request);
        $this->container->setParameter('users_orders', null);

        $this->mockUserManager->expects($this->once())
            ->method('searchUserByNamePaginated')
            ->with('search_value', 1, null, null, null)
            ->willReturn($this->pagerMock);

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->usersAction($request,1);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
