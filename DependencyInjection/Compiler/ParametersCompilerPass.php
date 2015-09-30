<?php

namespace Ant\Bundle\ApiSocialBundle\DependencyInjection\Compiler;

use Ant\Bundle\ApiSocialBundle\DependencyInjection\Configuration;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class ParametersCompilerPass
 *
 * @package Ant\Bundle\ApiSocialBundle\DependencyInjection\Compiler
 */
class ParametersCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        $parameterService = null;
        $parameterServiceName = '';

        if($container->getParameter('ant_api_social.parameters_service') == Configuration::PARAMETER_SERVICE_ANT){
            if(!$container->hasDefinition('ant_web_site_parameters.services.web_sites_parameters_service')){
                throw new ServiceNotFoundException('ant_web_site_parameters.services.web_sites_parameters_service',Configuration::PARAMETER_SERVICE_ANT);
            }
            $parameterServiceName = 'ant_api_social.services.web_sites_parameters_service_wrapper';

        }else{
            $parameterServiceName = 'ant_api_social.services.parameters_service';
        }

        $parameterService = new Reference($parameterServiceName,ContainerInterface::NULL_ON_INVALID_REFERENCE, false);

        $container->setDefinition('ant_parameters_service',$container->getDefinition($parameterServiceName));
        $twigDefinition= $container->getDefinition('twig');
        $twigDefinition->addMethodCall('addGlobal',
            array('web_param',$parameterService)
        );
    }
}