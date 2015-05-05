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
     * Show user Profile
     *
     * @param int $user_id
     *
     * @param string $username
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($username, $user_id = null)
    {
        $user = null;
        $isXmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $user = $this->findUserByUsernameOrId($username, $user_id, $isXmlHttpRequest);

        return $this->renderOrRedirect($user, $isXmlHttpRequest);
    }

    private function findUserByUsernameOrId($username, $user_id, $asArray)
    {
        if($user_id != null){
            return $this->get('api_users')->findById($user_id, $asArray);
        }else{
            return $this->get('api_users')->findById($username, $asArray);
        }
    }

    private function renderOrRedirect($user, $isXmlHttpRequest)
    {
        if ($this->mustRedirect($user)){
            return $this->redirect($this->generateUrl(
                'ant_user_user_show',
                array('username' => $user->getUsernameCanonical(), 'user_id' => $user->getId()))
            );
        }
        $params_to_template = array('user' => $user);

        if ($this->container->hasParameter('affiliate_id')){
            $params_to_template['affiliate_id'] = $this->container->getParameter('affiliate_id');
        }

        return $this->render('ApiSocialBundle:User:show.html.twig', $params_to_template);
    }

    /**
     * @return bool if the the first page was requested by url
     */
    private function pageOneGivenByUrl()
    {
        $request = $this->getRequest();

        return $request->get('page') == 1;
    }

    private function mustRedirect($user)
    {
        $request = $this->getRequest();
        $username = $request->get('username');
        $user_id = $request->get('user_id');

        return $user_id == null || $user->getUsernameCanonical() != $username;
    }
}