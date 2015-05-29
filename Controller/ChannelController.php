<?php
/**
 * Created by PhpStorm.
 * User: ant4
 * Date: 5/05/15
 * Time: 9:28
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Ant\Bundle\ChateaClientBundle\Api\Model\Channel;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChannelController extends Controller
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

    public function renderBreadcrumbAction(Channel $channel)
    {
        $channelsItems = array();
        $this->prepareChannels($channel,$channelsItems);

        return $this->render('ApiSocialBundle:Channel:renderBreadcrumb.html.twig',
            array(
                'channelsItems'=>$channelsItems)
        );
    }

    //returns if the the first page was requested by url
    private function pageOneGivenByUrl()
    {
        $request = $this->getRequest();

        return $request->get('_route') == 'channel_list_page' &&
        $request->get('page') == 1;
    }

    /**
     * a partir de channelType=2,name=aaaaa los separamos y añadimos los filtros
     * @param unknown $filterString
     * @return multitype:
     */
    private function getFilters($filterString)
    {
        $delimiters = array(',','=');

        $filters = $this->multiexplode($delimiters, $filterString);


        return $filters;
    }

    /**
     *
     * @param array $delimiters
     * @param string $string string to explode
     * @return multitype:
     * @throws BadRequestHttpException
     */
    private function multiexplode(array $delimiters, $string)
    {
        $type = explode($delimiters[0], $string);
        foreach ($type as $pair){
            if (!strpos($pair, $delimiters[1])){
                throw new BadRequestHttpException('invalid_request');
            }
            list($k, $v) = explode($delimiters[1], $pair);
            $result[$k] = $v;
        }
        return $result;
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
}