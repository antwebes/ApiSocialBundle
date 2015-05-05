<?php 
namespace Ant\Bundle\ApiSocialBundle\Twig\Extension;

class ChannelPhotoUrlExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('channelPhotoUrl', array($this, 'channelPhotoUrl')),
        );
    }

    public function getName()
    {
        return 'channel_photo_url';
    }
	//TODO remover extensión, posiblemente no haga falta
    public function channelPhotoUrl($channel, $size)
    {
        if(($photo = $channel->getPhoto()) != null){
            return $this->getSizePath($photo, $size);
        } else {
            return "";
        }
    }

    private function getSizePath($profilePhoto, $size)
    {
        $getter = 'getPath'.ucfirst($size);

        if(method_exists($profilePhoto, $getter)){
            return $profilePhoto->$getter();
        }

        return '';
    }
}   