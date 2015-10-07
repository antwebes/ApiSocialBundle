<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\EventListener;


use Twig_Environment;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class TwigParameterListener
 *
 * @package Ant\Bundle\ApiSocialBundle\EventListener;
 */
class TwigParameterListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $globalParameters = array();

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(Twig_Environment $twig, array $globalParameters = null)
    {
        $this->twig = $twig;
        $this->globalParameters = $globalParameters;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'OnRequestAddTwigGlobals');
    }

    public function OnRequestAddTwigGlobals(GetResponseEvent $event)
    {
        if($this->globalParameters != null){
            foreach($this->globalParameters as $key=>$value){
                $this->twig->addGlobal($key,$value);
            }
        }
    }
}