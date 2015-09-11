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
    /**
     * @test
     */
    public function testUsersActionSearchAction()
    {

        $containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $mockUserManager =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\UserManager')
                ->disableOriginalConstructor()
                ->getMock();

        $mockUserManager =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\UserManager')
                ->disableOriginalConstructor()
                ->getMock();
        $pagerMock =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Api\Util\Pager')
                ->disableOriginalConstructor()
                ->getMock();


        $templatingMock =
            $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
                ->disableOriginalConstructor()
                ->getMock();

        $request = new Request();
        $request->query->set('search','search_value');

        $container = new ContainerBuilder();
        $container->set('request',$request);
        $container->set('api_users',$mockUserManager);
        $container->set('templating',$templatingMock);
        $container->setParameter('users.language','en');

        $userController = new UserController();
        $userController->setContainer($container);

        $mockUserManager->expects($this->once())
            ->method('searchUserByNamePaginated')
            ->with('search_value')
            ->willReturn($pagerMock);

        $templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $userController->usersAction($request,1);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
