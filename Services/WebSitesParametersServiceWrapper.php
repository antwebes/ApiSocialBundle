<?php
/*
 * This file is part of the  chatBoilerplate package.
 *
 * (c) Ant web <ant@antweb.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ant\Bundle\ApiSocialBundle\Services;

use Ant\Bundle\ApiSocialBundle\Services\ParametersServiceInterface;
use Ant\WebSiteParametersBundle\Services\WebSitesParametersServiceInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * Class WebSitesParametersServiceWrapper
 *
 * @package Ant\Bundle\ApiSocialBundle\Services;
 */
class WebSitesParametersServiceWrapper implements ParametersServiceInterface
{

    /**
     * @var WebSitesParametersServiceInterface
     */
    private $webSitesParametersService;

    /**
     * WebSitesParametersService constructor.
     *
     * @param WebSitesParametersServiceInterface $webSitesParametersService
     */
    public function __construct(WebSitesParametersServiceInterface $webSitesParametersService)
    {
        $this->webSitesParametersService = $webSitesParametersService;
    }

    /***
     * Get parameter value
     *
     * @param string $parameterType the parameter type
     *
     * @param string $parameterName the parameter name
     *
     * @return string the parameter value the parameter value
     *
     * @throws ParameterNotFoundException this exception is throw if parameterType or  $parameterName not exits in
     *                                    collection
     */
    public function getParameter($parameterType, $parameterName)
    {
        return $this->webSitesParametersService->getParameter($parameterType, $parameterName);
    }

    /***
     * Has parameter exits in collection
     *
     * @param string $parameterType the parameter type
     *
     * @param string $parameterName the parameter name
     *
     * @return bool the parameter is defined in collection
     *
     */
    public function hasParameter($parameterType, $parameterName)
    {
        return $this->webSitesParametersService->hasParameter($parameterType, $parameterName);
    }

    /**
     * Clears all parameters.
     */
    public function clear()
    {
        $this->webSitesParametersService->clear();
    }

    /**
     * Gets the parameters for one website.
     *
     * @param string $parameterType the type of parameter, the default value is Twig, the valid values are Container and Twig
     *
     * @return array An array of parameters
     *
     */
    public function all($parameterType = null)
    {
        return $this->webSitesParametersService->all($parameterType);
    }


    /**
     * Returns true if in collection exists one parameter type defined.
     *
     * @param string $parameterType the type of parameter, the default value is Twig, the valid values are Container and Twig
     *
     * @return bool true if the parameter name is defined, false otherwise
     *
     */
    public function hasParameterType($parameterType)
    {
        return $this->webSitesParametersService->hasParameterType($parameterType);
    }


    /**
     * This method is used for access to parameter key. This find keys in all parameter
     * types and return the first occurrence of parameter key if is exists, in other case,  return null.
     *
     * @param string $name the parameter key
     *
     * @return null|string the parameter value
     */
    public function __call($name, $argumets)
    {
        return $this->webSitesParametersService->__call($name, $argumets);
    }

}