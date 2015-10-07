<?php
/**
 * Created by PhpStorm.
 * User: ant4
 * Date: 5/05/15
 * Time: 9:28
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Ant\Bundle\ApiSocialBundle\Form\ReportPhotoType;
use Symfony\Component\HttpFoundation\Request;
use Ant\Bundle\ChateaClientBundle\Security\Authentication\Annotation\APIUser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends BaseController
{
    public function usersAction(Request $request, $page = 1)
    {
        $search = $request->get('search',null);
        $advancedSearch = $request->get('advanced_search', null);

        if($this->pageOneGivenByUrl()){
            return $this->redirect($this->generateUrl('ant_user_user_users'));
        }
        $filters = array('language' => $this->container->getParameter('users.language'));
        
        $filter = $request->get('filter');
        if ($filter == 'customize'){
            $globals = $this->container->get('twig')->getGlobals();
            if (array_key_exists('id_affiliate', $globals)) {
                $filters['affiliate'] = $globals['id_affiliate'];
            }            
        }

        if($advancedSearch != null){
            if(isset($advancedSearch['country']) && !empty($advancedSearch['country'])){
                $filters['country'] = $advancedSearch['country'];
            }

            if(isset($advancedSearch['gender']) && !empty($advancedSearch['gender'])){
                $filters['gender'] = $advancedSearch['gender'];
            }
        }

        $usersManager = $this->get('api_users');

        if($search != null){
            $users = $usersManager->searchUserByNamePaginated($search, $page, $filter, null, $this->getDefaultOrder());
        }else{
            $users = $usersManager->findAll($page, $filters, null, $this->getDefaultOrder());
        }

        $outstandingUsers = $usersManager->findOutstandingUsers(5);

        return $this->render('ApiSocialBundle:User:List/index.html.twig',
            array(
                'users' => $users,
                'outstandingUsers' => $outstandingUsers,
                'search' => $search,
                'advancedSearch' => $advancedSearch
            )

        );
    }

    /**
     * Show user Profile
     *
     * @param int $user_id
     *
     * @param string $username
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($username, $user_id = null)
    {
        $user = null;
        $isXmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $user = $this->findUserByUsernameOrId($username, $user_id, $isXmlHttpRequest);

        return $this->renderOrRedirect($user, $isXmlHttpRequest);
    }

    /**
     * @param Request $request
     * @param int $page
     * @param int $withPagination
     * @param array $order
     * @param null $amount
     * @param null $size_image
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderWidgetUsersAction(Request $request, $page = 1, $withPagination = 1, $order = array('name' => 'asc'), $amount = null, $size_image=null)
    {
        //Si estamos con filtros hay que enviar filtros a la pagina siguiente
        $filter = $request->get('filter');

        if ($filter) {
            $filter = $this->getFilters('language='.$this->container->getParameter("users.language").','.$filter);
        } else {
            $filter = $this->getFilters('language='.$this->container->getParameter('users.language'));
        }

        $pager = $this->get('api_users')->findAll($page, $filter, $amount, $order);
        $users = $pager->getResources();

        if ($withPagination){
            $params = array('users' => $users, 'pager' => $pager);
        }else{
            $params = array('users' => $users);
        }

        $params['size_image'] = $size_image;

        return $this->render('ApiSocialBundle:User:Common/_renderWidgetUsers.html.twig', $params);
    }


    public function channelsAction($userId, $list)
    {
        $user = $this->get('api_users')->findById($userId);

        if($user == null){
            throw $this->createNotFoundException('The user with id ' .$userId, ' not exits');
        }

        $channels = array();

        if($list == 'owner'){
            $channels = $user->getChannels();
        }elseif($list == 'fan'){
            $channels = $user->getChannelsFan();
        }elseif($list == 'moderator'){
            $channels = $user->getChannelsModerated();
        }

        return $this->render('ApiSocialBundle:User:Channel/channels.html.twig',array('user'=>$user,'channels'=>$channels, 'type'=>$list));
    }
    
    public function renderWidgetUserSessionAction()
    {
    	$user_session = $this->get('security.context')->getToken()->getUser();
    	$user = $this->get('api_users')->findById($user_session->getId());
    	
    	return $this->render('ApiSocialBundle:User:Common/_widget_user_session.html.twig', array('user'=>$user));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function messagesAction()
    {
        return $this->render('ApiSocialBundle:User:messages.html.twig');
    }
    
    /*
     * @ToDo this function and function above, in future merge and configure.
     */
    public function renderWidgetPhotoIconUserSessionAction()
    {
    	$user_session = $this->get('security.context')->getToken()->getUser();
    	$user = $this->get('api_users')->findById($user_session->getId());
    	 
    	return $this->render('ApiSocialBundle:User:Common/_widget_photo_icon_user_session.html.twig', array('user'=>$user));
    }
    
    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function messageAction($id)
    {
    	$user = $this->get('api_users')->findById($id);
    	if($user == null){
    		throw $this->createNotFoundException('THe user with id ' .$id, ' not exits');
    	}
    	return $this->render('ApiSocialBundle:User:message.html.twig',array('user'=>$user));
    }

    /**
     * 
     * @param array $options
     * @param array $optionsDesign | array('btn-delete' : false|true , 'style' : index|rightbar , 'btn-search': 'white | success')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderAdvancedSearchAction($options = array(), $optionsDesign = array())
    {
    	//by default btn delete has exist
    	if (!array_key_exists('btn-delete', $optionsDesign)){
    		$optionsDesign['btn-delete'] = true;
    	}
    	
        $countriesPath = $this->container->getParameter('chatea_client.countries_file');
        $countries = $this->loadCountries($countriesPath);
        $userOnline = $this->getOnlineUserFromApi();
        $selectedCountry = '';
        $selectedGender = '';

        if(isset($options['gender'])){
            $selectedGender = $options['gender'];
        }

        if(isset($options['country'])){
            $selectedCountry = $options['country'];
        }else if($userOnline != null){
            $city = $userOnline->getCity();

            if($city !== null){
                $selectedCountry = $this->findCodeOfCountry($city->getCountry(), $countries);
            }
        }

        return $this->render('ApiSocialBundle:User:Common/advanced_user_search.html.twig',
            array(
                'countries'			=> $countries,
                'selectedCountry' 	=> $selectedCountry,
                'selectedGender' 	=> $selectedGender,
            	'optionsDesign'		=> $optionsDesign
            ));
    }

    private function findUserByUsernameOrId($username, $user_id, $asArray)
    {
        if($user_id != null){
            return $this->get('api_users')->findById($user_id, $asArray);
        }else{
            return $this->get('api_users')->findById($username, $asArray);
        }
    }

    private function renderOrRedirect($user, $isXmlHttpRequest)
    {
        if ($this->mustRedirect($user)){
            return $this->redirect($this->generateUrl(
                'ant_user_user_show',
                array('username' => $user->getUsernameCanonical(), 'user_id' => $user->getId()))
            );
        }
        $params_to_template = array(
            'user' => $user,
            'api_endpoint' => $this->container->getParameter('api_endpoint'),
            'voyeur_limit' => $this->container->getParameter('api_social.voyeur_limit')
        );

        if ($this->container->hasParameter('affiliate_id')){
            $params_to_template['affiliate_id'] = $this->container->getParameter('affiliate_id');
        }

        return $this->render('ApiSocialBundle:User:Show/show.html.twig', $params_to_template);
    }

    /**
     * @return bool if the the first page was requested by url
     */
    private function pageOneGivenByUrl()
    {
        $request = $this->getRequest();

        return $request->get('page') == 1;
    }

    private function mustRedirect($user)
    {
        $request = $this->getRequest();
        $username = $request->get('username');
        $user_id = $request->get('user_id');

        return $user_id == null || $user->getUsernameCanonical() != $username;
    }

    /**
     * @return array
     */
    protected function getDefaultOrder()
    {
        return $this->container->getParameter('users_orders');
    }

    private function loadCountries($countriesPath)
    {
        $content = json_decode(file_get_contents($countriesPath), true);

        $builder = function($entry){
            $country = $entry['name'];
            $value = $entry['country_code'];

            return array('value' => $value, 'name' => $country);
        };

        return array_map($builder, $content);
    }

    /**
     * @return array
     */
    protected function getOnlineUserFromApi()
    {
        $userOnline = $this->getUser();

        if($userOnline === null){
            return null;
        }

        /** @var \Ant\Bundle\ChateaClientBundle\Manager\UserManager $userManager */
        $userManager = $this->container->get('api_users');

        $user = $userManager->findById($userOnline->getId());

        return $user;
    }

    protected function findCodeOfCountry($countryName, $countries)
    {
        $country = array_filter($countries, function($country) use ($countryName){
            return $countryName == $country['name'];
        });

        if(count($country) > 0){
            return array_pop($country)['value'];
        }

        return '';
    }
}