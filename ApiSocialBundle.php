<?php

namespace Ant\Bundle\ApiSocialBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiSocialBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('ant.api_social.root_dir',__DIR__);
        $container->setParameter('ant.api_social.images_dir',__DIR__.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'images');
    }
}
