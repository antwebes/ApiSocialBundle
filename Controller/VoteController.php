<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ant\Bundle\ChateaClientBundle\Security\Authentication\Annotation\APIUser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VoteController extends Controller
{

    /**
     * @APIUser()
     */
    public function showVoteAction()
    {
        $user = $this->getUser();

        if(!$user->isValidated()){
            throw new AccessDeniedHttpException();
        }

        $request = $this->container->get('request');
        $translator = $this->container->get('translator');


        $gender = $request->get($translator->trans('gender'));


        return $this->render('ApiSocialBundle:Vote:photo.html.twig',

            array('gender'=>$gender
            )
        );
    }

}