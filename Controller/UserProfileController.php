<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ant\Bundle\ChateaClientBundle\Security\Authentication\Annotation\APIUser;

/**
 * Class UserProfileController
 *
 * @package Ant\Bundle\ApiSocialBundle\Controller;
 */
class UserProfileController extends BaseController
{


    /**
     * @return \Ant\Bundle\ApiSocialBundle\Util\PhotoProfileDefault
     */
    protected function getProfilePhotoUtility()
    {
        return $this->container->get('ant_api_social.util.photo_profile');

    }

    /**
     * Get profile photo for one user
     *
     * @param string $username
     * @param string $size
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     */
    public function getUserProfilePhotoAction($username, $size)
    {
        $user = $this->findApiEntityOrThrowNotFoundException('api_users', 'findById', $username);

        // if profile photo redirect url api
        if($user->getProfilePhoto() != null){
            $urlRedirect = '';

            switch ($size){
                case 'full':
                    $urlRedirect = $user->getProfilePhoto()->getPath();
                    break;
                case 'large':
                    $urlRedirect = $user->getProfilePhoto()->getPathLarge();
                    break;
                case 'medium':
                    $urlRedirect = $user->getProfilePhoto()->getPathMedium();
                    break;
                case 'small':
                    $urlRedirect = $user->getProfilePhoto()->getPathSmall();
                    break;
                case 'icon':
                    $urlRedirect = $user->getProfilePhoto()->getPathIcon();
                    break;
                default:
                    $urlRedirect = $user->getProfilePhoto()->getPathLarge();
                    break;
            }

            return $this->redirect($urlRedirect);

        }

        // Generate response
        $response = new Response();
        $profilePhotoUtility = $this->getProfilePhotoUtility();
        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-Type', $profilePhotoUtility->getMineType($size));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $profilePhotoUtility->getBasename($size). '";');
        $response->headers->set('Content-length', $profilePhotoUtility->getFilesize($size) );

        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent($profilePhotoUtility->readfile($size));

        return $response;
    }

    /**
     * @param $username
     * @param null $user_id
     * @return mixed
     * @ApiUser
     */
    public function showAllVisitsAction($username, $user_id = null)
    {
        $user = null;
        $isXmlHttpRequest = $this->getRequest()->isXmlHttpRequest();
        $user = $this->findUserByUsernameOrId($username, $user_id, $isXmlHttpRequest);

        if($user == null){
            throw new NotFoundHttpException();
        }

        if($user->getId() !== $this->getUser()->getId()){
            throw new AccessDeniedHttpException();
        }

        $params_to_template = array(
            'user' => $user,
            'api_endpoint' => $this->container->getParameter('api_endpoint'),
            'voyeur_limit' => $this->container->getParameter('api_social.voyeur_limit')
        );

        if ($this->container->hasParameter('affiliate_id')){
            $params_to_template['affiliate_id'] = $this->container->getParameter('affiliate_id');
        }

        return $this->render('ApiSocialBundle:User:showAllVisits.html.twig', $params_to_template);
    }

    private function findUserByUsernameOrId($username, $user_id, $asArray)
    {
        if($user_id != null){
            return $this->get('api_users')->findById($user_id, $asArray);
        }else{
            return $this->get('api_users')->findById($username, $asArray);
        }
    }
}