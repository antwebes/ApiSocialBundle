<?php
/**
 * Created by PhpStorm.
 * User: ant4
 * Date: 5/05/15
 * Time: 9:28
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    public function usersAction($page = 1)
    {
        if($this->pageOneGivenByUrl()){
            return $this->redirect($this->generateUrl('ant_user_user_users'));
        }

        $users = $this->get('api_users')->findAll($page, array('language' => $this->container->getParameter('users.language') ));

        return $this->render('ApiSocialBundle:User:index.html.twig', array('users' => $users));
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
            $filter = $this->getFilters('language=es,'.$filter);
        } else {
            $filter = $this->getFilters('language=es');
        }

        $pager = $this->get('api_users')->findAll($page, $filter, $amount, $order);
        $users = $pager->getResources();

        if ($withPagination){
            $params = array('users' => $users, 'pager' => $pager);
        }else{
            $params = array('users' => $users);
        }

        $params['size_image'] = $size_image;

        return $this->render('ApiSocialBundle:User:_renderWidgetUsers.html.twig', $params);
    }


    public function channelsAction($userId, $list)
    {
        $user = $this->get('api_users')->findById($userId);

        if($user == null){
            throw $this->createNotFoundException('THe user with id ' .$userId, ' not exits');
        }

        $channels = array();

        if($list == 'owner'){
            $channels = $user->getChannels();
        }elseif($list == 'fan'){
            $channels = $user->getChannelsFan();
        }elseif($list == 'moderator'){
            $channels = $user->getChannelsModerated();
        }

        return $this->render('ApiSocialBundle:Show:channels.html.twig',array('user'=>$user,'channels'=>$channels, 'type'=>$list));
    }

    public function photosAction($id, $page = 1)
    {
        $user = $this->get('api_users')->findById($id);

        if($user == null){
            throw $this->createNotFoundException('THe user with id ' .$id, ' not exits');
        }

        $photos = $user->getPhotos($page);

        return $this->render('ApiSocialBundle:Show:photos.html.twig',array('user'=>$user,'photos'=> $photos));
    }

    public function showPhotoAction($idUser, $id, $page = 1)
    {
        $user = $this->get('api_users')->findById($idUser);
        $photo = $this->get('api_photos')->findById($id);

        if($user == null){
            throw $this->createNotFoundException('THe user with id ' .$idUser, ' not exits');
        }

        if($photo == null || $photo->getParticipant()->getUsername() != $user->getUsername()){
            throw $this->createNotFoundException('THe photo with id ' .$id, ' not exits');
        }



        return $this->render('ApiSocialBundle:Show:photo.html.twig',array('user'=>$user,'photo'=> $photo));
    }
    
    public function renderWidgetUserSessionAction()
    {
    	$user_session = $this->get('security.context')->getToken()->getUser();
    	$user = $this->get('api_users')->findById($user_session->getId());
    	
    	return $this->render('ApiSocialBundle:User:_widget_user_session.html.twig', array('user'=>$user));
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
        $params_to_template = array('user' => $user);

        if ($this->container->hasParameter('affiliate_id')){
            $params_to_template['affiliate_id'] = $this->container->getParameter('affiliate_id');
        }

        return $this->render('ApiSocialBundle:User:show.html.twig', $params_to_template);
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
}