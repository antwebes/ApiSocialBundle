<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Tests\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Ant\Bundle\ApiSocialBundle\Tests\WebTestCase;
use Ant\Bundle\ApiSocialBundle\Controller\UserProfileController;
use Ant\Bundle\ChateaClientBundle\Api\Model\User;
use Ant\Bundle\ChateaClientBundle\Api\Model\UserProfile;
use Ant\Bundle\ChateaClientBundle\Api\Model\Photo;



class UserProfileControllerTest extends WebTestCase
{
    private function getImageDirPath()
    {
        return __DIR__ . '/../../Resources/public/images';
    }



    /**
     * @test
     */
    public function testGetUserProfilePhotoAction()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $apiUserMock = $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\User')
            ->disableOriginalConstructor()
            ->setMethods(array('findById'))
            ->getMock();


        $controller = new UserProfileController();
        $controller->setContainer($containerMock);

        $photo = new Photo();

        $photo->setPathLarge('http://server.api/photo-large.jpg');

        $userProfile = new UserProfile();
        $userProfile->setProfilePhoto($photo);
        $user = new User();
        $user->setProfile($userProfile);

        $containerMock->expects($this->once())
            ->method('get')
            ->with('api_users')
            ->willReturn($apiUserMock);

        $apiUserMock->expects($this->once())
            ->method('findById')
            ->with('foo')
            ->willReturn($user);

        $response = $controller->getUserProfilePhotoAction('foo','large');

        $this->assertEquals(302,$response->getStatusCode());
        $this->assertEquals('http://server.api/photo-large.jpg',$response->getTargetUrl());
    }

    /**
     * @test
     */
    public function testGetUserProfilePhotoActionWithDefaultPhoto()
    {
        $size = 'large';

        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $apiUserMock = $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\User')
            ->disableOriginalConstructor()
            ->setMethods(array('findById'))
            ->getMock();

        $controller = new UserProfileController();
        $controller->setContainer($containerMock);

        $user = new User();
        $user->setProfile(new UserProfile());

        $photoProfileDefaultMock = $this->getMockBuilder('Ant\Bundle\ApiSocialBundle\Util\PhotoProfileDefault')
            ->disableOriginalConstructor()
            ->getMock();


        $controller = new UserProfileController();
        $controller->setContainer($containerMock);


        $containerMock->expects($this->at(0))
            ->method('get')
            ->with('api_users')
            ->willReturn($apiUserMock);


        $containerMock->expects($this->at(1))
            ->method('get')
            ->with('ant_api_social.util.photo_profile')
            ->willReturn($photoProfileDefaultMock);



        $apiUserMock->expects($this->once())
            ->method('findById')
            ->with('foo')
            ->willReturn($user);


        $photoProfileDefaultMock
            ->expects($this->once())
            ->method('getMineType')
            ->with($size)
            ->willReturn('image/png');

        $photoProfileDefaultMock
            ->expects($this->once())
            ->method('getBasename')
            ->with($size)
            ->willReturn('image.png');

        $photoProfileDefaultMock
            ->expects($this->once())
            ->method('getFilesize')
            ->with($size)
            ->willReturn(1588);

        $photoProfileDefaultMock
            ->expects($this->once())
            ->method('readfile')
            ->with($size)
            ->willReturn(1);

        $response = $controller->getUserProfilePhotoAction('foo',$size);

        $this->assertEquals(200,$response->getStatusCode());
        $this->assertEquals('image/png',$response->headers->get('Content-Type'));
        $this->assertEquals(1,$response->getContent());
    }

    public function testGetUserProfilePhotoActionUserNotFoundException()
    {
        $size = 'large';

        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $apiUserMock = $this->getMockBuilder('Ant\Bundle\ChateaClientBundle\Manager\User')
            ->disableOriginalConstructor()
            ->setMethods(array('findById'))
            ->getMock();

        $controller = new UserProfileController();
        $controller->setContainer($containerMock);


        $controller = new UserProfileController();
        $controller->setContainer($containerMock);


        $containerMock->expects($this->at(0))
            ->method('get')
            ->with('api_users')
            ->willReturn($apiUserMock);



        $apiUserMock->expects($this->once())
            ->method('findById')
            ->with('foo')
            ->willReturn(null);

        try{
            $controller->getUserProfilePhotoAction('foo',$size);

        }catch (NotFoundHttpException $e){
            return;
        }
        $this->fail('Expect throw expcetion NotFoundHttpException');

    }
}
