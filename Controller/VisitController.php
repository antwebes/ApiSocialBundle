<?php

namespace Ant\Bundle\ApiSocialBundle\Controller;

class VisitController extends BaseController
{
    public function renderWidgetUserVisitorsAction($user, $limit)
    {
        $userManager = $this->get('api_users');
        $visitors = $userManager->getUserVisits($user, $limit);

        return $this->render('ApiSocialBundle:Visit:_widgetVisitors.html.twig', array('visitors' => $visitors));
    }
}