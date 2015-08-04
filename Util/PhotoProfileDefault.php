<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Util;

use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class PhotoProfile
 *
 * @package Ant\Bundle\ApiSocialBundle\Util;
 */
class PhotoProfileDefault
{
    private static $valid_sizes = array('full','large','medium','small','icon');

    /**
     * @var string
     */
    private $imagesDir;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * PhotoProfile constructor.
     * @param string $imagesDir the directory with default images
     */
    public function __construct(Filesystem $filesystem, $imagesDir)
    {
        $this->imagesDir = $imagesDir;
        $this->filesystem = $filesystem;
    }

    /**
     * Return full path for default profile photo
     *  Only are available this sizes: full|large|medium|small|icon
     *
     * @param string $size The size for default image.
     *
     * @return string the full path for file;
     *
     * @throws \RuntimeException this throw if file for default image not exists
     *
     * @throws \InvalidArgumentException this exception is throw when parameter size is not valid
     */
    public function getFilename($size)
    {
        if(! in_array($size,self::$valid_sizes)){
            throw new \InvalidArgumentException('The parameter size: '.$size.' is not valid, only are available this sizes: full|large|medium|small|icon');
        }

        $filename = '';

        switch ($size){
            case 'full':
            case 'large':
                $filename =  $this->imagesDir."/no-photo-large.png";
                break;
            case 'medium':
                $filename =  $this->imagesDir."/no-photo-medium.png";
                break;
            case 'small':
                $filename =  $this->imagesDir."/no-photo-small.png";
                break;
            case 'icon':
                $filename =  $this->imagesDir."/no-photo-icon.png";
                break;
            default:
                $filename =  $this->imagesDir."/no-photo-large.png";
                break;
        }

        if(! $this->filesystem->exists($filename)){
            throw new \RuntimeException('The file: ' . $filename . ' not exits');
        }

        return $filename;

    }
    /**
     *  Get default profile photo
     *
     *  Only are available this sizes: full|large|medium|small|icon
     *
     * @param string $size The size for default image.
     *
     * @return int the number of bytes read from the file. If an error
     * occurs, false is returned and unless the function was called as
     *               
     * @throws \RuntimeException this throw if file for default image not exists
     *
     * @throws \InvalidArgumentException this exception is throw when parameter size is not valid
     */
    public function readfile($size)
    {
        return readfile($this->getFilename($size));

    }

    /**
     * Returns the mime type of the file for default profile photo.
     *
     *  Only are available this sizes: full|large|medium|small|icon
     *
     * @param string $size The size for default image.
     *
     * @return string|null The guessed mime type (i.e. "application/pdf")
     *
     * @throws \RuntimeException this throw if file for default image not exists
     *
     * @throws \InvalidArgumentException this exception is throw when parameter size is not valid
     */
    public function getMineType($size)
    {
        $file = new File($this->getFilename($size));

        return $file->getMimeType();
    }

    /**
     * Gets the base name for default profile photo.
     *
     *  Only are available this sizes: full|large|medium|small|icon
     *
     * @param string $size The size for default image.
     *
     * @return string The base name without path information.
     *
     * @throws \RuntimeException this throw if file for default image not exists
     *
     * @throws \InvalidArgumentException this exception is throw when parameter size is not valid
     */
    public function getBasename($size)
    {
        $splFileInfo = new SplFileInfo($this->getFilename($size));
        return $splFileInfo->getBasename();
    }

    /**
     * Gets file size for default profile photo.
     *
     * Only are available this sizes: full|large|medium|small|icon
     *
     * @param string $size The size for default image.
     *
     * @return int the size of the file in bytes, or false
     *
     * @throws \RuntimeException this throw if file for default image not exists
     *
     * @throws \InvalidArgumentException this exception is throw when parameter size is not valid
     */
    public function getFilesize($size)
    {
        return filesize($this->getFilename($size));
    }
}