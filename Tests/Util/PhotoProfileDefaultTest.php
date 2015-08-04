<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Tests\Util;

use Ant\Bundle\ApiSocialBundle\Util\PhotoProfileDefault;
use Symfony\Component\Filesystem\Filesystem;


/**
 * Class PhotoProfileDefaultTest
 */
class PhotoProfileDefaultTest extends \PHPUnit_Framework_TestCase
{

    private function getImageDirPath()
    {
        return __DIR__ . '/../../Resources/public/images';
    }

    public function getValidSizes()
    {
        return array(
            array('full'),
            array('large'),
            array('medium'),
            array('small'),
            array('icon')
        );
    }

    public function getInValidSizes()
    {
        return array(
            array('xxl'),
            array('xl'),
            array('l'),
            array('m'),
            array('s')
        );
    }

    /**
     * @test
     * @dataProvider getValidSizes
     */
    public function testGetFilename($size)
    {
        $photoProfileDefault = new PhotoProfileDefault(new Filesystem(), $this->getImageDirPath());
        $filename = $photoProfileDefault->getFilename($size);
        $this->assertFileExists($filename);
    }

    /**
     * @test
     * @dataProvider getInValidSizes
     */
    public function testGetFilenameInvalidSizes($size)
    {
        $photoProfileDefault = new PhotoProfileDefault(new Filesystem(), $this->getImageDirPath());
        try{
            $photoProfileDefault->getFilename($size);
        }catch (\InvalidArgumentException $e){
            return;
        }
        $this->fail('Expected to throw exception InvalidArgumentException ');


    }

    /**
     * @test
     * @dataProvider getValidSizes
     */
    public function testGetFilenameImagesNotExists($size)
    {
        $photoProfileDefault = new PhotoProfileDefault(new Filesystem(), __DIR__);
        try{
            $photoProfileDefault->getFilename($size);
        }catch (\RuntimeException $e){
            return;
        }
        $this->fail('Expected to throw exception RuntimeException');
    }

    /**
     * @test
     * @dataProvider getValidSizes
     */
    public function testGetMineType($size)
    {
        $photoProfileDefault = new PhotoProfileDefault(new Filesystem(), $this->getImageDirPath());
        $mime_type = $photoProfileDefault->getMineType($size);
        $this->assertEquals("image/png",$mime_type);
    }

    /**
     * @test
     */
    public function testGetBasename()
    {
        $photoProfileDefault = new PhotoProfileDefault(new Filesystem(), $this->getImageDirPath());
        $baseName= $photoProfileDefault->getBasename('medium');
        $this->assertEquals("no-photo-medium.png",$baseName);
    }

    /**
     * @test
     */
    public function testGetFilesize()
    {
        $photoProfileDefault = new PhotoProfileDefault(new Filesystem(), $this->getImageDirPath());
        $fileSize= $photoProfileDefault->getFilesize('medium');
        $this->assertEquals(1588,$fileSize);
    }
}
