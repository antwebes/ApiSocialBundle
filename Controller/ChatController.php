<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends Controller
{

    public function showAction(Request $request)
    {
    	$params = $this->getRequest()->query->all();
    	
    	//Esto se hace mientras que el script del chat, no funcione correctamente, ahora sustituye # por %23 y luego 
    	//hace encodeUri, entonces envia %2523, por eso le quitamos la #
    	if (array_key_exists('channel', $params)){
    		if (substr($params['channel'], 0, 1 ) === "#"){
    			$params['channel'] = substr($params['channel'], 1);
    		}
    	}
    	//if user logued join chat with its username
    	if ($this->getUser()){
    		$params['nick'] = $this->getUser()->getUsername();
    	}
    	
    	$params_string = http_build_query($params);
    	
        return $this->render('ApiSocialBundle:Chat:show.html.twig', array('params_string' => $params_string, 'params_array' => $params));
    }

}