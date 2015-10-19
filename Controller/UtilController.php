<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UtilController extends Controller {

    public function generateBlockAdsenseAction($size = null, $styles = array('hr'=> 1))
    {
        
        $parameters_service = $this->get('ant_api_social.services.parameters_service');
        
        if(!$parameters_service->hasParameter("container","adsense")) {
            return new Response();
        }

        $adsense = $parameters_service->getParameter("container", "adsense");
    	
    	$env = $this->container->getParameter("kernel.environment");
    	if ($adsense && ($env !='dev')){
    		$lotery = rand(1, 100);
    		if ($lotery>60){
    			return $this->render('ApiSocialBundle:Adsense:adsense-awc.html.twig', array('size'=> $size, 'styles' => $styles));
    		}else{
    			return $this->render('ApiSocialBundle:Adsense:adsense-affiliate.html.twig', array('size'=> $size, 'styles' => $styles));
    		}
    	}
    	return new Response();
    }
} 