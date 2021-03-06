<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Ant\Bundle\ApiSocialBundle\Form\ReportPhotoType;
use Symfony\Component\HttpFoundation\Request;
use Ant\Bundle\ChateaClientBundle\Security\Authentication\Annotation\APIUser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class PhotoController extends BaseController
{
    /**
     * @param $id
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function photosAction($id, $page = 1)
    {
        $user = $this->get('api_users')->findById($id);
        $isUserValidated = $this->getUser()->isValidated();

        if($user == null){
            throw $this->createNotFoundException('THe user with id ' .$id, ' not exits');
        }

        if($isUserValidated) {
            $photos = $user->getPhotos($page);
        }else{
            $photos = array();
        }

        $paramsToTemplate = array(
            'user' => $user,
            'photos'=> $photos,
            'voyeur_limit' => $this->container->getParameter('api_social.voyeur_limit'),
            'realtime_endpoint' => $this->container->getParameter('api_social.realtime_endpoint')
        );

        return $this->render('ApiSocialBundle:Photo:List/photos.html.twig', $paramsToTemplate);
    }

    public function topPhotosAction($page)
    {
        $minimumVotesForPopularPhotos = $this->container->getParameter('minimum_votes_for_popular_photos');
        
        //ñapa necesaria para no mostrar mas de 5 páginas
        if ($page > 5){
        	return $this->redirect($this->generateUrl('popular_photos', array('page'=> 5)));
        }
        
        $photos = $this->get('api_photos')->findAll($page, array('number_votes_greater_equal' => $minimumVotesForPopularPhotos), null, array('score' => 'desc'));

        return $this->render('ApiSocialBundle:Photo:List/popularphotos.html.twig',array('photos'=> $photos));
    }

    /**
     * @param $idUser
     * @param $id
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function showPhotoAction($idUser, $id, $page = 1)
    {
        $user = $this->findApiEntityOrThrowNotFoundException('api_users', 'findById', $idUser);
        $photo = $this->get('api_photos')->findById($id);

        if($photo == null || $photo->getParticipant()->getUsername() != $user->getUsername()){
            throw $this->createNotFoundException('THe photo with id ' .$id. ' not exits');
        }

        if(!$this->getUser()->isValidated()){
            return $this->redirect($this->generateUrl('ant_user_user_photos_show', array('id' => $idUser)));
        }

        return $this->render('ApiSocialBundle:Photo:Show/photo.html.twig',array('user'=>$user,'photo'=> $photo));
    }

    /**
     * @param $idUser
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function removePhotoAction($idUser, $id)
    {
        $user = $this->findApiEntityOrThrowNotFoundException('api_users', 'findById', $idUser, 'The user with id ' .$idUser. ' not exits');
        $photo = $this->get('api_photos')->findById($id);

        if($photo == null || $photo->getParticipant()->getUsername() != $user->getUsername()){
            throw $this->createNotFoundException('The photo with id ' .$id. ' not exits');
        }

        if($this->getUser()->getId() !== $user->getId()){
            throw new AccessDeniedHttpException($this->translate('user.photo.remove.not_owner'));
        }

        try{
            $this->get('api_photos')->delete($photo->getId());

            $this->addFlash('notice', $this->translate('user.photo.remove.success', array(), 'User'));

            return $this->redirect($this->generateUrl('ant_user_user_photos_show', array('id' => $user->getId())));
        }catch(\Exception $e){
            $this->addFlash('error', $this->translate('user.photo.remove.failure', array(), 'User'));

            return $this->redirect($this->generateUrl('ant_user_user_photo_show', array('idUser' => $user->getId(), 'id' => $photo->getId())));
        }
    }
    
     /**
     * @param $idUser
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @APIUser()
     */
    public function reportPhotoAction(Request $request, $idUser, $id)
    {
        $photosManager = $this->get('api_photos');
        $user = $this->findApiEntityOrThrowNotFoundException('api_users', 'findById', $idUser, 'The user with id ' .$idUser. ' not exits');
        $photo = $photosManager->findById($id);
        $form = $this->createForm(new ReportPhotoType());
        $success = false;
        $apiError = null;

        if($photo == null || $photo->getParticipant()->getUsername() != $user->getUsername()){
            throw $this->createNotFoundException('THe photo with id ' .$id, ' not exits');
        }

        if($request->isMethod('POST')){
            $form->submit($request);
            if($form->isValid()){
                $data = $form->getData();

                try{
                    $photosManager->reportPhoto($photo, $data['reason']);
                    $success = true;
                }catch(\Exception $e){
                    try{
                        $error = json_decode($e->getMessage(), true);

                        if(isset($error['errors'])){
                            $apiError = $this->translateServerError($error['errors']);
                        }
                    }catch(\Exception $ee   ){

                    }

                }
            }
        }

        $templateVars = array(
            'form' => $form->createView(),
            'user' => $user,
            'photo' => $photo,
            'success' => $success,
            'apiError' => $apiError
        );

        return $this->render('ApiSocialBundle:Photo:Report/reportPhoto.html.twig', $templateVars);
    }

    private function translate($message, $parameters = array(), $domain = null)
    {
        return $this->get('translator')->trans($message, $parameters, $domain);
    }

    private function translateServerError($errorMessage, $translationContext = 'User')
    {
        $translator = $this->get('translator');
        $errorMapping = array(
            "The report already exists" => "user.photos.report.allready_reported",
        );

        if(isset($errorMapping[$errorMessage])){
            return $translator->trans($errorMapping[$errorMessage], array(), $translationContext);
        }

        return $errorMessage;
    }
}