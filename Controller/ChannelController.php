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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChannelController extends BaseController
{
    public function channelsAction(Request $request, $page = 1)
    {
        $filter = $request->get('filter');


        if($this->pageOneGivenByUrl()){
            return $this->redirect($this->generateUrl('channel_list'));
        }

        return $this->render('ApiSocialBundle:Channel:channels.html.twig', array('page' => $page, 'filter' => $filter, 'size_image' => 'small'));
    }

    public function showAction($slug)
    {
        $channel = $this->get('api_channels')->findBySlug($slug);

        return $this->render('ApiSocialBundle:Channel:show.html.twig', array('channel' => $channel));
    }

    /**
     * @APIUser
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function becomeFanAction($slug)
    {
        $channelsManager = $this->get('api_channels');
        $channel = $channelsManager->findBySlug($slug);
        $user = $this->getUser();

        try{
            $channelsManager->addFanToChannel($user, $channel);
            $this->addFlash('notice', $this->get('translator')->trans('channels.fan_added_success', array(), 'Channels'));
        }catch (\Exception $e){
            try{
                $message = json_decode($e->getMessage(), true);

                if($message['errors'] == 'The user already a fan of this channel'){
                    $this->addFlash('error', $this->get('translator')->trans('channels.fan_allready_fan', array(), 'Channels'));
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
    public function renderChannelsAction(Request $request, $page = 1, $withPagination = 1, $order = array('name' => 'asc'), $amount = null, $size_image=null)
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

        return $this->render('ApiSocialBundle:Channel:_renderChannels.html.twig', $params);
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
    
    	return $this->render('ApiSocialBundle:Channel:_renderWidgetChannels.html.twig', $params);
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

        return $this->render('ApiSocialBundle:Channel:usersFans.html.twig', array('users' => $users));
    }

    //returns if the the first page was requested by url
    private function pageOneGivenByUrl()
    {
        $request = $this->getRequest();

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

    private function addFlash($level, $message)
    {
        $request = $this->getRequest();

        $request->getSession()->getFlashBag()->add(
            $level,
            $message
        );
    }
}