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

use Ant\Bundle\ApiSocialBundle\Controller\ChannelController;
use Ant\Bundle\ChateaClientBundle\Api\Model\Channel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class ChannelControllerTest
 *
 * @package Ant\Bundle\ApiSocialBundle\Tests\Controller
 */
class ChannelControllerTestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function renderChannelsAction()
    {
        $mockChannelManager =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\ChannelManager')
                ->disableOriginalConstructor()
                ->getMock();
        $pagerMock =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Api\Util\Pager')
                ->disableOriginalConstructor()
                ->getMock();
        $twigMock =
            $this->getMockBuilder('Twig_Environment')
                ->disableOriginalConstructor()
                ->getMock();
        $templatingMock =
            $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
                ->disableOriginalConstructor()
                ->getMock();

        $channelCollection = array(new Channel(),new Channel());
        $request = new Request();

        $container = new ContainerBuilder();
        $container->set('request',$request);
        $container->set('api_channels',$mockChannelManager);
        $container->set('templating',$templatingMock);
        $container->set('twig',$twigMock);
        $channelController = new ChannelController();
        $channelController->setContainer($container);

        $mockChannelManager->expects($this->once())
            ->method('findAll')
            ->with(1,array('language'=>'es'),null,array('countVisits' => 'desc'))
            ->willReturn($pagerMock);

        $pagerMock->expects($this->once())
            ->method('getResources')
            ->willReturn($channelCollection);

        $view = 'ApiSocialBundle:Channel:List/_renderChannels.html.twig';

        $parameters = array('channels'=>$channelCollection,'pager'=>$pagerMock,'size_image'=>'small', 'search' => null);
        $templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with($view,$parameters,null)
            ->willReturn(new Response());
        $response = $channelController->renderChannelsAction($request,1,1,array('countVisits' => 'desc'), null, 'small');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
