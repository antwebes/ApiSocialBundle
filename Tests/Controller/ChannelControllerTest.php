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
use Ant\Bundle\ApiSocialBundle\DependencyInjection\ApiSocialExtension;
use Ant\Bundle\ChateaClientBundle\Api\Model\Channel;
use Ant\Bundle\ChateaClientBundle\Api\Model\User;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ChannelControllerTest
 *
 * @package Ant\Bundle\ApiSocialBundle\Tests\Controller
 */
class ChannelControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $twigMock;
    protected $templatingMock;
    protected $routerMock;
    protected $container;
    protected $channelController;
    protected $mockChannelManager;
    protected $translator;
    protected $session;
    protected $securitContext;
    protected $tokenMock;

    protected function setUp()
    {
        parent::setUp();

        $this->twigMock =
            $this->getMockBuilder('Twig_Environment')
                ->disableOriginalConstructor()
                ->getMock();

        $this->templatingMock =
            $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
                ->disableOriginalConstructor()
                ->getMock();

        $this->mockChannelManager =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\ChannelManager')
                ->disableOriginalConstructor()
                ->getMock();

        $this->routerMock = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');

        $this->tokenMock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');


        $tokenStorageMock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $tokenStorageMock->expects($this->any())
            ->method('getToken')
            ->willReturn($this->tokenMock);

        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->securityContext->expects($this->any())
            ->method('getToken')
            ->willReturn($this->tokenMock);

        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');

        $this->session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $this->session->expects($this->any())
            ->method('getFlashBag')
            ->willReturn($flashBag);

        $this->container = new ContainerBuilder();
        $this->container->set('twig', $this->twigMock);
        $this->container->set('templating',$this->templatingMock);
        $this->container->set('router', $this->routerMock);
        $this->container->set('api_channels', $this->mockChannelManager);
        $this->container->set('translator', $this->translator);
        $this->container->set('security.token_storage', $tokenStorageMock);
        $this->container->set('security.context', $this->securityContext);
        $this->container->setParameter('api_endpoint', 'http://an.api.com');

        $this->channelController = new ChannelController();
        $this->channelController->setContainer($this->container);
    }

    protected function mockRouteGeneration($route, $parmeters, $url)
    {
        $this->routerMock->expects($this->once())
            ->method('generate')
            ->with($route, $parmeters)
            ->will($this->returnValue($url));
    }

    protected function mockTemplatingRenderResponse($view, $parameters, $response = null)
    {
        if($response === null){
            $response = new Response();
        }

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with($view,$parameters,null)
            ->willReturn($response);
    }

    protected function mockSessionUser($user)
    {
        $this->tokenMock->expects($this->any())
            ->method('getUser')
            ->willReturn($user);
    }

    /**
     * @test
     */
    public function testChannelActionSearchAction()
    {

        $this->mockRouteGeneration('channel_list', array('search' => 'search_value', 'page' => 1), 'anurl');

        $request = new Request();
        $request->query->set('search','search_value');
        $request->query->set('page','1');
        $request->attributes->set('_route', 'channel_list_page');

        $this->container->set('request',$request);
        
        $response = $this->channelController->channelsAction($request,1);

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function renderChannelsActionWithSearchParam()
    {
        $pagerMock =
            $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Api\Util\Pager')
                ->disableOriginalConstructor()
                ->getMock();

        $channelCollection = array(new Channel(),new Channel());

        $request = new Request();
        $request->query->set('search','search_value');

        $this->container->set('request',$request);

        $this->mockChannelManager->expects($this->once())
            ->method('findAll')
            ->with(1,array('language'=>'es','partialName'=>'search_value'),null,array('name' => 'asc'))
            ->willReturn($pagerMock);

        $pagerMock->expects($this->once())
            ->method('getResources')
            ->willReturn($channelCollection);

        $view = 'ApiSocialBundle:Channel:List/_renderChannels.html.twig';
        $parameters = array('channels'=>$channelCollection,'pager'=>$pagerMock,'size_image'=>'small','search'=>'search_value');

        $this->mockTemplatingRenderResponse($view, $parameters);

        $response = $this->channelController->renderChannelsAction($request,1,1,array('name' => 'asc'), null, 'small');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function showAction()
    {
        $channel = new Channel();

        $request = new Request();
        $request->query->set('search','search_value');

        $this->container->set('request',$request);

        $this->mockChannelManager->expects($this->once())
            ->method('findBySlug')
            ->with('a-channel-slug')
            ->willReturn($channel);

        $view = 'ApiSocialBundle:Channel:Show/show.html.twig';
        $parameters = array('channel' => $channel, 'api_endpoint' => $this->container->getParameter('api_endpoint'));

        $this->mockTemplatingRenderResponse($view, $parameters);

        $response = $this->channelController->showAction('a-channel-slug');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function showActionNotFound()
    {
        $request = new Request();
        $request->query->set('search','search_value');

        $this->container->set('request',$request);

        $this->mockChannelManager->expects($this->once())
            ->method('findBySlug')
            ->with('a-channel-slug')
            ->willReturn(null);

        try{
            $response = $this->channelController->showAction('a-channel-slug');
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }

    /**
     * @test
     */
    public function becomeFanAction()
    {
        $user = new User();
        $channel = new Channel();
        $channel->setSlug('a-channel-slug');

        $request = new Request();
        $request->query->set('search','search_value');
        $request->setSession($this->session);

        $this->mockSessionUser($user);

        $this->container->set('request', $request);

        $this->mockChannelManager->expects($this->once())
            ->method('findBySlug')
            ->with('a-channel-slug')
            ->willReturn($channel);
        $this->mockChannelManager->expects($this->once())
            ->method('addFanToChannel')
            ->with($user, $channel);

        $this->mockRouteGeneration('channel_show', array('slug' => 'a-channel-slug'), '/channels/a-channel-slug');

        $response = $this->channelController->becomeFanAction('a-channel-slug');

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function becomeFanActionNotFound()
    {
        $channel = new Channel();
        $channel->setSlug('a-channel-slug');

        $request = new Request();
        $request->query->set('search','search_value');

        $this->mockChannelManager->expects($this->once())
            ->method('findBySlug')
            ->with('a-channel-slug')
            ->willReturn(null);
        $this->mockChannelManager->expects($this->never())
            ->method('addFanToChannel');

        try{
            $response = $this->channelController->becomeFanAction('a-channel-slug');
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }

    /**
     * @test
     */
    public function removeFanAction()
    {
        $user = new User();
        $channel = new Channel();
        $channel->setSlug('a-channel-slug');

        $request = new Request();
        $request->query->set('search','search_value');
        $request->setSession($this->session);

        $this->mockSessionUser($user);

        $this->container->set('request', $request);

        $this->mockChannelManager->expects($this->once())
            ->method('findBySlug')
            ->with('a-channel-slug')
            ->willReturn($channel);
        $this->mockChannelManager->expects($this->once())
            ->method('delUserChannelFan')
            ->with($user, $channel);

        $this->mockRouteGeneration('channel_show', array('slug' => 'a-channel-slug'), '/channels/a-channel-slug');

        $response = $this->channelController->removeFanAction('a-channel-slug');

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function removeFanActionNotFound()
    {
        $channel = new Channel();
        $channel->setSlug('a-channel-slug');

        $request = new Request();
        $request->query->set('search','search_value');

        $this->mockChannelManager->expects($this->once())
            ->method('findBySlug')
            ->with('a-channel-slug')
            ->willReturn(null);
        $this->mockChannelManager->expects($this->never())
            ->method('delUserChannelFan');

        try{
            $response = $this->channelController->removeFanAction('a-channel-slug');
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }
}
