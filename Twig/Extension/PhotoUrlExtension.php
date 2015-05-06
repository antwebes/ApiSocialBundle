<?php 
namespace Ant\Bundle\ApiSocialBundle\Twig\Extension;

class PhotoUrlExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('photoUrl', array($this, 'photoUrl')),
        );
    }

    public function getName()
    {
        return 'photo_url';
    }

	//TODO remover extensión, posiblemente no haga falta
    public function photoUrl($photo, $size)
    {
        if($photo != null){
            return $this->getSizePath($photo, $size);
        } else {
            return "";
        }
    }

    private function getSizePath($photo, $size)
    {
        $getter = 'getPath'.ucfirst($size);

        if(method_exists($photo, $getter)){
            return $photo->$getter();
        }

        return '';
    }
}   