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

use Ant\Bundle\ChateaClientBundle\Api\Model\City;
use Ant\Bundle\ChateaClientBundle\Api\Model\User;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Ant\Bundle\ApiSocialBundle\Controller\UserController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserControllerTest
 *
 * @package Ant\Bundle\ApiSocialBundle\Tests\Controller
 */
class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    private $mockUserManager;
    private $templatingMock;
    private $pagerMock;
    private $securityContextMock;
    private $container;
    private $userController;
    private $countriesPath;

    protected function setUp()
    {
        parent::setUp();

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
        $this->securityContextMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $this->countriesPath = __DIR__ . '/../fixtures/countries.json';

        $this->container = new ContainerBuilder();
        $this->container->set('api_users',$this->mockUserManager);
        $this->container->set('templating',$this->templatingMock);
        $this->container->set('security.context',$this->securityContextMock);
        $this->container->setParameter('users.language','en');
        $this->container->setParameter('chatea_client.countries_file', $this->countriesPath);
        $this->container->setParameter('api_social.api_endpoint', 'http://an.api.com');

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

        $this->mockUserManager->expects($this->once())
            ->method('findOutstandingUsers')
            ->with(array('language' => 'en'));

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

        $this->mockUserManager->expects($this->once())
            ->method('findOutstandingUsers')
            ->with(array('partial_name' => 'search_value', 'language' => 'en'));

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->usersAction($request,1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testShowAction()
    {
        $request = new Request();
        $request->query->set('username', 'alex');
        $request->query->set('user_id', 1);

        $user = new User();
        $user->setId(1);
        $user->setUsername('alex');
        $user->setUsernameCanonical('alex');

        $this->container->set('request',$request);
        $this->container->setParameter('api_endpoint', 'http://un.api.com/');
        $this->container->setParameter('api_social.voyeur_limit', 1);
        $this->container->setParameter('api_social.realtime_endpoint', 'http://realtime.api.com');


        $this->mockUserManager->expects($this->once())
            ->method('findById')
            ->with(1, false)
            ->willReturn($user);

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->showAction('alex',1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testShowActionAsAjax()
    {
        $request = new Request();
        $request->query->set('username', 'alex');
        $request->query->set('user_id', 1);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $user = new User();
        $user->setId(1);
        $user->setUsername('alex');
        $user->setUsernameCanonical('alex');

        $this->container->set('request',$request);
        $this->container->setParameter('api_endpoint', 'http://un.api.com/');
        $this->container->setParameter('api_social.voyeur_limit', 1);
        $this->container->setParameter('api_social.realtime_endpoint', 'http://realtime.api.com');


        $this->mockUserManager->expects($this->once())
            ->method('findById')
            ->with(1, true)
            ->willReturn($user);

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->showAction('alex',1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testShowActionRedirect()
    {
        $request = new Request();
        $request->query->set('username', 'alex');
        $request->query->set('user_id', 1);

        $user = new User();
        $user->setId(1);
        $user->setUsername('alex2');
        $user->setUsernameCanonical('alex2');

        $routerGenerator = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $routerGenerator->expects($this->once())
            ->method('generate')
            ->with(
                'ant_user_user_show',
                array('username' => $user->getUsernameCanonical(), 'user_id' => $user->getId())
            )
            ->will($this->returnValue('http://anhost.com/an-url'));

        $this->container->set('request',$request);
        $this->container->set('router', $routerGenerator);
        $this->container->setParameter('api_endpoint', 'http://un.api.com/');
        $this->container->setParameter('api_social.voyeur_limit', 1);
        $this->container->setParameter('api_social.realtime_endpoint', 'http://realtime.api.com');


        $this->mockUserManager->expects($this->once())
            ->method('findById')
            ->with(1, false)
            ->willReturn($user);

        $response = $this->userController->showAction('alex',1);

        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testShowActionOfNonExistingUser()
    {
        $request = new Request();
        $request->query->set('username', 'alex');
        $request->query->set('user_id', 1);

        $this->container->set('request',$request);
        $this->container->setParameter('api_endpoint', 'http://un.api.com/');
        $this->container->setParameter('api_social.voyeur_limit', 1);
        $this->container->setParameter('api_social.realtime_endpoint', 'http://realtime.api.com');


        $this->mockUserManager->expects($this->once())
            ->method('findById')
            ->with(1, false)
            ->willReturn(null);

        try{
            $response = $this->userController->showAction('alex',1);
        }catch(NotFoundHttpException $e){
            return;
        }

        $this->fail('Expected an NotFoundHttpException exception');
    }

    /**
     * @test
     */
    public function testUsersActionSearchAdvancedAction()
    {
        $request = new Request();
        $request->query->set('advanced_search', array('country' => 'US', 'gender' => 'Male'));
        $this->container->set('request',$request);
        $this->container->setParameter('users_orders', array('lastLogin' => 'desc', 'hasProfilePhoto' => 'desc'));

        $this->mockUserManager->expects($this->once())
            ->method('findAll')
            ->with(1, array('country' => 'US', 'gender' => 'Male', 'language' => 'en'), null, array('lastLogin' => 'desc', 'hasProfilePhoto' => 'desc'))
            ->willReturn($this->pagerMock);

        $this->mockUserManager->expects($this->once())
            ->method('findOutstandingUsers')
            ->with(array('country' => 'US', 'gender' => 'Male', 'language' => 'en'));

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->willReturn(new Response());

        $response = $this->userController->usersAction($request,1);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function testRenderAdvancedSearchAction()
    {
        $countries = $this->loadCountries();
        $parameters = array(
                'countries' => $countries,
                'selectedCountry' => '',
                'selectedGender' => '',
                'optionsDesign' => array('btn-delete' => true)
            );

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with('ApiSocialBundle:User:Common/advanced_user_search.html.twig', $parameters)
            ->willReturn(new Response());

        $this->userController->renderAdvancedSearchAction();
    }

    /**
     * @test
     */
    public function testRenderAdvancedSearchActionWithLogedinUser()
    {
        $countries = $this->loadCountries();
        $this->configureUserWithCity();

        $parameters = array(
            'countries' => $countries,
            'selectedCountry' => 'DZ',
            'selectedGender' => '',
            'optionsDesign' => array('btn-delete' => true)
        );

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with('ApiSocialBundle:User:Common/advanced_user_search.html.twig', $parameters)
            ->willReturn(new Response());

        $this->userController->renderAdvancedSearchAction();
    }

    /**
     * @test
     */
    public function testRenderAdvancedSearchActionWithLogedinUserAndOptions()
    {
        $countries = $this->loadCountries();
        $this->configureUserWithCity();

        $parameters = array(
            'countries' => $countries,
            'selectedCountry' => 'AX',
            'selectedGender' => 'Male',
            'optionsDesign' => array('btn-delete' => true)
        );

        $this->templatingMock->expects($this->once())
            ->method('renderResponse')
            ->with('ApiSocialBundle:User:Common/advanced_user_search.html.twig', $parameters)
            ->willReturn(new Response());

        $this->userController->renderAdvancedSearchAction(array('country' => 'AX', 'gender' => 'Male', ));
    }

    private function loadCountries()
    {
        $content = json_decode(file_get_contents($this->countriesPath), true);

        $builder = function($entry){
            $country = $entry['name'];
            $value = $entry['country_code'];

            return array('value' => $value, 'name' => $country);
        };

        return array_map($builder, $content);
    }

    private function configureUserWithCity()
    {
        $city = new City();
        $city->setName('a city');
        $city->setCountry('Algeria');

        $user = new User();
        $user->setId(1);
        $user->setUsername('ausername');
        $user->setCity($city);

        $this->mockUserManager->expects($this->once())
            ->method('findById')
            ->with(1)
            ->will($this->returnValue($user));

        $onlineUser = $this->getMockBuilder('Ant\Bundle\ChateaSecureBundle\Security\User\User')
            ->disableOriginalConstructor()
            ->getMock();
        $onlineUser->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($onlineUser));

        $this->securityContextMock->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token));
    }
}
