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

class UserController extends BaseController
{
    public function usersAction(Request $request, $page = 1)
    {
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

        $usersManager = $this->get('api_users');


        $users = $usersManager->findAll($page, $filters);
        $outstandingUsers = $usersManager->findOutstandingUsers(5);

        return $this->render('ApiSocialBundle:User:index.html.twig', array('users' => $users, 'outstandingUsers' => $outstandingUsers));
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

    public function renderWidgetUserVisitorsAction($user, $limit)
    {
        $userManager = $this->get('api_users');
        $visitors = $userManager->getUserVisits($user, $limit);

        return $this->render('ApiSocialBundle:User:widgetVisitors.html.twig', array('visitors' => $visitors));
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

    /**
     * @param $id
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function photosAction($id, $page = 1)
    {
        $user = $this->get('api_users')->findById($id);
        $isUserValidated = $this->getUser()->isValidated();

        if($user == null){
            throw $this->createNotFoundException('THe user with id ' .$id, ' not exits');
        }

        if($isUserValidated) {
            $photos = $user->getPhotos($page);
        }else{
            $photos = array();
        }

        return $this->render('ApiSocialBundle:Show:photos.html.twig',array('user'=>$user,'photos'=> $photos));
    }

    /**
     * @param $idUser
     * @param $id
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
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

        if(!$this->getUser()->isValidated()){
            return $this->redirect($this->generateUrl('ant_user_user_photos_show', array('id' => $idUser)));
        }

        return $this->render('ApiSocialBundle:Show:photo.html.twig',array('user'=>$user,'photo'=> $photo));
    }
    
    public function renderWidgetUserSessionAction()
    {
    	$user_session = $this->get('security.context')->getToken()->getUser();
    	$user = $this->get('api_users')->findById($user_session->getId());
    	
    	return $this->render('ApiSocialBundle:User:_widget_user_session.html.twig', array('user'=>$user));
    }

    /**
     * @param $idUser
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function reportPhotoAction(Request $request, $idUser, $id)
    {
        $photosManager = $this->get('api_photos');
        $user = $this->get('api_users')->findById($idUser);
        $photo = $photosManager->findById($id);
        $form = $this->createForm(new ReportPhotoType());
        $success = false;
        $apiError = null;

        if($user == null){
            throw $this->createNotFoundException('THe user with id ' .$idUser, ' not exits');
        }

        if($photo == null || $photo->getParticipant()->getUsername() != $user->getUsername()){
            throw $this->createNotFoundException('THe photo with id ' .$id, ' not exits');
        }

        if($request->isMethod('POST')){
            $form->submit($request);
            if($form->isValid()){
                $data = $form->getData();

                try{
                    $photosManager->reportPhoto($photo, $data['reason']);
                    $success = true;
                }catch(\Exception $e){
                    try{
                        $error = json_decode($e->getMessage(), true);

                        if(isset($error['errors'])){
                            $apiError = $this->translateServerError($error['errors']);
                        }
                    }catch(\Exception $ee   ){

                    }

                }
            }
        }

        $templateVars = array(
            'form' => $form->createView(),
            'user' => $user,
            'photo' => $photo,
            'success' => $success,
            'apiError' => $apiError
        );

        return $this->render('ApiSocialBundle:Show:reportPhoto.html.twig', $templateVars);
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
            'api_endpoint' => $this->container->getParameter('api_endpoint')
        );

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

    private function translateServerError($errorMessage, $translationContext = 'User')
    {
        $translator = $this->get('translator');
        $errorMapping = array(
            "The report already exists" => "user.photos.report.allready_reported",
        );

        if(isset($errorMapping[$errorMessage])){
            return $translator->trans($errorMapping[$errorMessage], array(), $translationContext);
        }

        return $errorMessage;
    }
}