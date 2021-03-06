<?php
/**
 * Created by PhpStorm.
 * User: ant4
 * Date: 5/05/15
 * Time: 9:28
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Ant\Bundle\ChateaClientBundle\Api\Model\Channel;
use Ant\Bundle\ChateaClientBundle\Security\Authentication\Annotation\APIUser;
use Ant\ChateaClient\Client\ApiException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChannelController extends BaseController
{
    public function channelsAction(Request $request, $page = 1)
    {
        $filter = $request->get('filter','');
        $search = $request->get('search',null);

        if($search != null){
            $filter = 'partialName='.$search;

        }
        $twig_globals = $this->getGlobalsTwig();

        //if not exist list_channels_customized of affiliate, redirect to url without filter=customize
        if ($filter == 'customize' && !array_key_exists('list_channels_customized', $twig_globals)){
            return $this->redirect($this->generateUrl('channel_list'));
        }


        if($this->pageOneGivenByUrl($request)){
            $parameters = array();
            if($search != null){
                $parameters['search']= $search;
                $parameters['page']= $page;
            }

            return $this->redirect($this->generateUrl('channel_list',$parameters));
        }

        return $this->render('ApiSocialBundle:Channel:List/channels.html.twig', array('page' => $page, 'filter' => $filter, 'size_image' => 'small','search'=>$search));
    }

    private function getGlobalsTwig()
    {
        $twig_globals = $this->container->get('twig')->getGlobals();

        return $twig_globals;
    }
    
    public function renderChannelsCustomizedAction(Request $request, $page = 1, $withPagination = 1, $order = array('name' => 'asc'), $amount = null, $size_image=null)
    {
        $twig_globals = $this->getGlobalsTwig();

        //if not exist list_channels_customized render all list channels
        //If not exist list, We should not we are here, renderChannels check if param exist, and if not exist redirect all list
        if (array_key_exists('list_channels_customized', $twig_globals)){
            $customize_channels = $twig_globals['list_channels_customized'];
            $array_customize_channels = explode(",", $customize_channels);
            $channelsNames = $array_customize_channels;
        }else{
            $response = $this->forward('ApiSocialBundle:Channel:renderChannels');
            return $response;
        }
        
    
    	if($this->pageOneGivenByUrl()){
    		return $this->redirect($this->generateUrl('channel_list'));
    	}
    	
    	$channels = array();
    
    	foreach($channelsNames as $channelName){
            //if some channel of list_channels_customized doesnt exist, not show error, channel wont show in template
            try {
    		  $channel = $this->get('api_channels')->findById($channelName);
            }catch(ApiException $ex){
                $message = json_decode($ex->getMessage(), true);
                //If the error is that the channel is not found, continue with the following channel
                if ($message['code'] != '32'){
                    throw $ex;
                }
                continue;
            }
    		array_push($channels,$channel);
    	}

    	$params = array('channels' => $channels);
    	
    	$size_image = 'small';
    	
    	$params['size_image'] = $size_image;
    	
    	return $this->render('ApiSocialBundle:Channel:List/_renderChannels.html.twig', $params);

        return $this->render('ApiSocialBundle:Channel:List/channels.html.twig', array('page' => $page, 'filter' => $filter, 'size_image' => 'small'));

    }

    public function showAction($slug)
    {
        $channel = $this->findApiEntityOrThrowNotFoundException('api_channels', 'findBySlug', $slug);

        return $this->render('ApiSocialBundle:Channel:Show/show.html.twig', array('channel' => $channel, 'api_endpoint' => $this->container->getParameter('api_endpoint')));
    }

    /**
     * @APIUser
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function becomeFanAction($slug)
    {
        $channelsManager = $this->get('api_channels');
        $channel = $this->findApiEntityOrThrowNotFoundException('api_channels', 'findBySlug', $slug);
        $user = $this->getUser();

        try{
            $channelsManager->addFanToChannel($user, $channel);
            $this->addFlash('notice', $this->get('translator')->trans('channels.fan_added_success', array(), 'Channels'));
        }catch (ApiException $e){
            try{
                $message = json_decode($e->getMessage(), true);                
                if(is_array($message) && isset($message['errors']) && $message['errors'] == 'The user already a fan of this channel'){
                    $this->addFlash('error', $this->get('translator')->trans('channels.fan_allready_fan', array(), 'Channels'));
                }elseif(is_string($message) && $message== 'This user is not validated. The user need to confirm your email'){
                    $this->addFlash('error', $this->get('translator')->trans('user.need_validate', array(), 'User'));
                }else{
                    $this->addFlash('error', $this->get('translator')->trans('channels.fan_error', array(), 'Channels'));
                }
            }catch (\Exception $ejson){
                $this->addFlash('error', $this->get('translator')->trans('channels.fan_error', array(), 'Channels'));
            }
        }

        return $this->redirect($this->generateUrl('channel_show', array('slug' => $channel->getSlug())));
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
    public function renderChannelsAction(Request $request, $page = 1, $withPagination = 1, $amount = null, $size_image=null)
    {
        //Si estamos con filtros hay que enviar filtros a la pagina siguiente
        $filter = $request->get('filter');
        $search = $request->get('search',null);
        $order = $this->container->getParameter('channels_orders');

        if ($filter == 'customize'){
            return $this->renderChannelsCustomizedAction($request, $page, $withPagination, $order, $amount, $size_image);
        }
        
        if ($filter) {
            $filter = $this->getFilters('language=es,'.$filter);
        } else {
            $filter = $this->getFilters('language=es');
        }

        if($search != null){
            $filter['partialName']= $search;
        }

        $pager = $this->get('api_channels')->findAll($page, $filter, $amount, $order);
        $channels = $pager->getResources();

        if ($withPagination){
            $params = array('channels' => $channels, 'pager' => $pager);
        }else{
            $params = array('channels' => $channels);
        }

        $params['size_image'] = $size_image;
        $params['search'] = $request->get('search',null);
        return $this->render('ApiSocialBundle:Channel:List/_renderChannels.html.twig', $params);
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
    public function renderWidgetChannelsAction(Request $request, $page = 1, $withPagination = 1, $order = array('name' => 'asc'), $amount = null, $size_image=null)
    {
    	//Si estamos con filtros hay que enviar filtros a la pagina siguiente
    	$filter = $request->get('filter');
    	if ($filter) {
    		$filter = $this->getFilters('language=es,'.$filter);
    	} else {
    		$filter = $this->getFilters('language=es');
    	}
    
    	$pager = $this->get('api_channels')->findAll($page, $filter, $amount, $order);
    	$channels = $pager->getResources();
    
    	if ($withPagination){
    		$params = array('channels' => $channels, 'pager' => $pager);
    	}else{
    		$params = array('channels' => $channels);
    	}
    
    	$params['size_image'] = $size_image;
    	return $this->render('ApiSocialBundle:Channel:List/_renderWidgetChannels.html.twig', $params);
    }
    

    public function renderBreadcrumbAction(Channel $channel)
    {
        $channelsItems = array();
        $this->prepareChannels($channel,$channelsItems);

        return $this->render('ApiSocialBundle:Channel:renderBreadcrumb.html.twig',
            array(
                'channelsItems'=>$channelsItems)
        );
    }

    /**
     *
     * @param int $numberOfUsers
     * @param string $columnClass
     * @param array $filter for example form twig: {'has_profile_photo' : 1}
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usersFansAction($id, $numberOfUsers, $filter = null)
    {
        //findFans($channel_id, $page=1, array $filters = null, $limit= null)
        $users = $this->get("api_channels")->findFans($id, 1, null, $numberOfUsers);

        return $this->render('ApiSocialBundle:Channel:Fan/usersFans.html.twig', array('users' => $users));
    }

    //returns if the the first page was requested by url
    private function pageOneGivenByUrl($request = null)
    {
        if($request == null){
            $request = $this->getRequest();
        }

        return $request->get('_route') == 'channel_list_page' &&
        $request->get('page') == 1;
    }


    /**
     * This method return the channels in order 1º the channel 2º: channel->getParent(), 3º  channel->getParent()->getParent() ..
     * @param Channel $channel
     * @param array $channelsItems
     */
    private function prepareChannels(Channel $channel = null,  array &$channelsItems)
    {
        if($channel == null){
            return;
        }else{
            $this->prepareChannels($channel->getParent(),$channelsItems);
            $channelsItems[] = $channel;
        }
    }

    /**
     * @APIUser
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeFanAction($slug)
    {
        $channelsManager = $this->get('api_channels');
        $channel = $this->findApiEntityOrThrowNotFoundException('api_channels', 'findBySlug', $slug);
        $user = $this->getUser();

        try{
            $channelsManager->delUserChannelFan($user, $channel);
            $this->addFlash('notice', $this->get('translator')->trans('channels.fan_remove_success', array(), 'Channels'));
        }catch (\Exception $e){
            try{
                $message = json_decode($e->getMessage(), true);

                if($message['errors'] == 'The user not is fan of channel'){
                    $this->addFlash('error', $this->get('translator')->trans('channels.fan.user_already_is_not_fan', array(), 'Channels'));
                }
            }catch (\Exception $ejson){
                $this->addFlash('error', $this->get('translator')->trans('channels.fan_error', array(), 'Channels'));
            }
        }

        return $this->redirect($this->generateUrl('channel_show', array('slug' => $channel->getSlug())));
    }
}