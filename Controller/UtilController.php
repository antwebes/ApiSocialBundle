<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UtilController extends Controller {

    public function generateBlockAdsenseAction($size = null)
    {
    	$twig = $this->container->get('twig');
    	$globals = $twig->getGlobals();
    	$env = $this->container->getParameter("kernel.environment");
    	if ($globals['adsense'] && ($env !='dev')){
    		$lotery = rand(1, 100);
    		if ($lotery>60){
    			return $this->render('ApiSocialBundle:Adsense:adsense-awc.html.twig', array('size'=> $size));
    		}else{
    			return $this->render('ApiSocialBundle:Adsense:adsense-affiliate.html.twig', array('size'=> $size));
    		}
    	}
    	return new Response();
    }
} 