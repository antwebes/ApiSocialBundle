<?php
/**
 * Created by PhpStorm.
 * User: ping
 * Date: 5/06/15
 * Time: 10:45
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class StatsController extends Controller {

    public function generalStatsAction()
    {

        $pager = $this->get('api_channels')->findAll(1);
        $num_channels = $pager->count();

        $pager = $this->get('api_users')->findAll(1);
        $num_users = $pager->count();

        return $this->render('ApiSocialBundle:Stats:general.html.twig', array('num_channels' => $num_channels, 'num_users' => $num_users ));
    }

} 