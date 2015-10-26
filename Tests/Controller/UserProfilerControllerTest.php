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

use Ant\Bundle\ApiSocialBundle\Controller\UserProfileController;
use Ant\Bundle\ChateaClientBundle\Api\Model\Photo;
use Ant\Bundle\ChateaClientBundle\Api\Model\User;
use Ant\Bundle\ChateaClientBundle\Api\Model\UserProfile;
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
class UserProfilerControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $twigMock;
    protected $templatingMock;
    protected $routerMock;
    protected $container;
    protected $userProfilerController;
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
        $this->container->set('templating',$this->templatingMock);
        $this->container->set('router', $this->routerMock);
        $this->container->set('api_photos', $this->mockPhotoManager);
        $this->container->set('api_users', $this->mockUserManager);
        $this->container->set('translator', $this->translator);
        $this->container->set('security.token_storage', $tokenStorageMock);
        $this->container->set('security.context', $this->securityContext);
        $this->container->setParameter('api_endpoint', 'http://an.api.com');

        $this->userProfilerController = new UserProfileController();
        $this->userProfilerController->setContainer($this->container);
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
    public function getUserProfilePhotoAction()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);
        $photo->setPathLarge('http://superimages.com/my-image.jpg');

        $profile = new UserProfile();
        $profile->setProfilePhoto($photo);

        $user->setProfile($profile);

        $sessionUser = $this->getMockWitoutConstructor('Ant\Bundle\ChateaSecureBundle\Security\User\User');

        $this->mockMethod($this->mockUserManager, 'findById', 'filtradoraDeArrays', $user);

        $request = new Request();
        $this->container->set('request',$request);
        
        $response = $this->userProfilerController->getUserProfilePhotoAction('filtradoraDeArrays', 'large');

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function getUserProfilePhotoActionNoUser()
    {
        $user = new User();
        $user->setId(302);
        $user->setUsername('filtradoraDeArrays');

        $photo = new Photo();
        $photo->setId(1);
        $photo->setParticipant($user);
        $photo->setPathLarge('http://superimages.com/my-image.jpg');

        $profile = new UserProfile();
        $profile->setProfilePhoto($photo);

        $user->setProfile($profile);

        $sessionUser = $this->getMockWitoutConstructor('Ant\Bundle\ChateaSecureBundle\Security\User\User');

        $this->mockMethod($this->mockUserManager, 'findById', 'filtradoraDeArrays', null);

        $request = new Request();
        $this->container->set('request',$request);

        try{
            $response = $this->userProfilerController->getUserProfilePhotoAction('filtradoraDeArrays', 'large');
        }catch (NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }
}
