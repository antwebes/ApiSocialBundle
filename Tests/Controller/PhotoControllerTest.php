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
use Ant\Bundle\ChateaClientBundle\Api\Model\Photo;
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
class PhotoControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $twigMock;
    protected $templatingMock;
    protected $routerMock;
    protected $container;
    protected $photoController;
    protected $mockPhotoManager;
    protected $mockUserManager;
    protected $translator;
    protected $session;
    protected $securitContext;
    protected $tokenMock;

    protected function setUp()
    {
        parent::setUp();

        $this->twigMock = $this->getMockWitoutConstructor('Twig_Environment');
        $this->templatingMock = $this->getMockWitoutConstructor('Symfony\Bundle\TwigBundle\TwigEngine');
        $this->mockPhotoManager = $this->getMockWitoutConstructor('Ant\Bundle\ChateaClientBundle\Manager\PhotoManager');
        $this->mockUserManager = $this->getMockWitoutConstructor('Ant\Bundle\ChateaClientBundle\Manager\UserManager');

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
        $this->container->set('session', $this->session);
        $this->container->set('templating',$this->templatingMock);
        $this->container->set('router', $this->routerMock);
        $this->container->set('api_photos', $this->mockPhotoManager);
        $this->container->set('api_users', $this->mockUserManager);
        $this->container->set('translator', $this->translator);
        $this->container->set('security.token_storage', $tokenStorageMock);
        $this->container->set('security.context', $this->securityContext);
        $this->container->setParameter('api_endpoint', 'http://an.api.com');

        $this->photoController = new PhotoController();
        $this->photoController->setContainer($this->container);
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

    protected function mockMethod($mock, $method, $arg, $return, $when = null)
    {
        if($when === null){
            $when = $this->any();
        }

        if($arg !== null) {
            $mock->expects($when)
                ->method($method)
                ->with($arg)
                ->will($this->returnValue($return));
        }else{
            $mock->expects($when)
                ->method($method)
                ->will($this->returnValue($return));
        }
    }

    protected function getMockWitoutConstructor($class)
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function showPhotoAction()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);

        $sessionUser = $this->getMockWitoutConstructor('Ant\Bundle\ChateaSecureBundle\Security\User\User');

        $this->mockMethod($this->mockUserManager, 'findById', 299, $user);
        $this->mockMethod($this->mockPhotoManager, 'findById', 1, $photo);
        $this->mockMethod($sessionUser, 'isValidated', null, true);
        $this->mockSessionUser($sessionUser);
        $this->mockTemplatingRenderResponse('ApiSocialBundle:Photo:Show/photo.html.twig',array('user'=>$user,'photo'=> $photo));

        $request = new Request();
        $request->query->set('search','search_value');
        $request->query->set('page','1');
        $request->attributes->set('_route', 'channel_list_page');

        $this->container->set('request',$request);
        
        $response = $this->photoController->showPhotoAction(299, 1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function showPhotoActionWithNoUser()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);
        $this->mockMethod($this->mockUserManager, 'findById', 299, null);
        $this->mockMethod($this->mockPhotoManager, 'findById', 1, $photo, $this->any());

        $request = new Request();
        $request->query->set('search','search_value');
        $request->query->set('page','1');
        $request->attributes->set('_route', 'channel_list_page');

        $this->container->set('request',$request);

        try{
            $response = $this->photoController->showPhotoAction(299, 1);
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }

    /**
     * @test
     */
    public function showPhotoActionWithNoPhoto()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);

        $this->mockMethod($this->mockUserManager, 'findById', 299, $user, $this->any());
        $this->mockMethod($this->mockPhotoManager, 'findById', 1, null);

        $request = new Request();
        $request->query->set('search','search_value');
        $request->query->set('page','1');
        $request->attributes->set('_route', 'channel_list_page');

        $this->container->set('request',$request);

        try{
            $response = $this->photoController->showPhotoAction(299, 1);
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }

    /**
     * @test
     */
    public function removePhotoAction()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);

        $sessionUser = $this->getMockWitoutConstructor('Ant\Bundle\ChateaSecureBundle\Security\User\User');
        $this->mockMethod($sessionUser, 'getId', null, 302);

        $this->mockSessionUser($sessionUser);

        $this->mockMethod($this->mockUserManager, 'findById', 299, $user);
        $this->mockMethod($this->mockPhotoManager, 'findById', 1, $photo);
        $this->mockMethod($this->mockPhotoManager, 'delete', 1, $photo);
        $this->mockMethod($sessionUser, 'isValidated', null, true);
        $this->mockSessionUser($sessionUser);
        $this->mockRouteGeneration('ant_user_user_photos_show', array('id' => $user->getId()), '/a/url');

        $request = new Request();
        $request->setSession($this->session);

        $this->container->set('request',$request);

        $response = $this->photoController->removePhotoAction(299, 1);

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function removePhotoActionWithNoUser()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);
        $this->mockMethod($this->mockUserManager, 'findById', 299, null);
        $this->mockMethod($this->mockPhotoManager, 'findById', 1, $photo, $this->any());

        $request = new Request();

        $this->container->set('request',$request);

        try{
            $response = $this->photoController->removePhotoAction(299, 1);
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }

    /**
     * @test
     */
    public function removePhotoActionWithNoPhoto()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);

        $this->mockMethod($this->mockUserManager, 'findById', 299, $user, $this->any());
        $this->mockMethod($this->mockPhotoManager, 'findById', 1, null);

        $request = new Request();
        $request->query->set('search','search_value');
        $request->query->set('page','1');
        $request->attributes->set('_route', 'channel_list_page');

        $this->container->set('request',$request);

        try{
            $response = $this->photoController->removePhotoAction(299, 1);
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }
}
