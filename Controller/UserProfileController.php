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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $user = $this->get('api_users')->findById($username);

        if($user == null){
            throw new NotFoundHttpException("The user with username  $username,  not exits");
        }

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
}