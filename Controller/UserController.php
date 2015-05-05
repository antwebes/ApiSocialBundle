<?php
/**
 * Created by PhpStorm.
 * User: ant4
 * Date: 5/05/15
 * Time: 9:28
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function usersAction($page = 1)
    {
        if($this->pageOneGivenByUrl()){
            return $this->redirect($this->generateUrl('ant_user_user_users'));
        }

        $users = $this->get('api_users')->findAll($page);

        return $this->render('ApiSocialBundle:User:index.html.twig', array('users' => $users));
    }


    /**
     * @return bool if the the first page was requested by url
     */
    private function pageOneGivenByUrl()
    {
        $request = $this->getRequest();

        return $request->get('page') == 1;
    }
}