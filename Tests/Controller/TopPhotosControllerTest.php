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

use Ant\Bundle\ApiSocialBundle\Controller\PhotoController;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TopPhotosControllerTest
 *
 * @package Ant\Bundle\ApiSocialBundle\Tests\Controller
 */
class TopPhotosControllerTest extends \PHPUnit_Framework_TestCase
{
    private $mockPhotoManager;
    private $containerMock;
    private $templatingMock;
    private $pagerMock;
    private $securityContextMock;
    private $container;
    private $photoController;

    protected function setUp()
    {
        parent::setUp();

        $this->containerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockPhotoManager =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\PhotoManager')
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
        $this->securityContextMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $this->container = new ContainerBuilder();
        $this->container->set('api_photos',$this->mockPhotoManager);
        $this->container->set('templating',$this->templatingMock);
        $this->container->set('security.context',$this->securityContextMock);

        $this->photoController = new PhotoController();
        $this->photoController->setContainer($this->container);
    }

    public function testTopPhotosAction()
    {
        $this->container->setParameter('minimum_votes_for_popular_photos', 3);

        $this->mockPhotoManager->expects($this->once())
            ->method('findAll')
            ->with(1, array('number_votes_greater_equal' => 3), null, array('score' => 'desc'))
            ->willReturn($this->pagerMock);

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->photoController->topPhotosAction(1);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
