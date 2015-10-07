<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Tests\EventListener;

use Ant\Bundle\ApiSocialBundle\EventListener\TwigParameterListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TwigParameterListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function OnRequestAddTwigGlobals()
    {
        $twigMock = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $listener = new TwigParameterListener($twigMock,array('foo'=>'bar'));

        $mockKernel = $this->getMock('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request();

        $event = new GetResponseEvent($mockKernel,$request,HttpKernelInterface::MASTER_REQUEST );


        $twigMock->expects($this->any())
            ->method('addGlobal')
            ->with('foo','bar');

        $listener->OnRequestAddTwigGlobals($event);

    }
}
