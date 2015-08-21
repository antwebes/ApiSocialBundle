<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ant\Bundle\ChateaClientBundle\Security\Authentication\Annotation\APIUser;

class VoteController extends Controller
{

    /**
     * @APIUser()
     */
    public function showVoteAction()
    {
        return $this->render('ApiSocialBundle:Vote:photo.html.twig');
    }

}