<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends Controller
{

    public function showAction(Request $request)
    {
    	$params = $this->getRequest()->query->all();
    	$params_string = http_build_query($params);
    	
        return $this->render('ApiSocialBundle:Chat:show.html.twig', array('params' => $params_string));
    }

}