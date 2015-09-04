<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

class VisitController extends BaseController
{
    public function renderWidgetUserVisitorsAction($user, $limit = false, $expand = false)
    {
        $userManager = $this->get('api_users');

        if($limit){
            $limit = $this->container->getParameter('visits_limit');
        }else{
            $limit = null;
        }

        $visitors = $userManager->getUserVisits($user, $limit);

        return $this->render('ApiSocialBundle:Visit:_widgetVisitors.html.twig', array('visitors' => $visitors, 'expand' => $expand));
    }
}