<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChatController extends Controller
{

    public function showAction()
    {
        return $this->render('ApiSocialBundle:Chat:show.html.twig', array());
    }

}