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

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * Class ParametersService
 *
 * @package Ant\Bundle\PrettyBundle\Services;
 */
class ParametersService extends FileLoader implements ParametersServiceInterface
{
    /**
     * @var array $parameters;
     */
    private $parameters;
    /**
     * @var string the file name
     */
    private $file;
    /**
     * @var string the header for parameters yml
     */
    private $header;

    /**
     * @var string the kernel environment
     */
    private $environment;

    /**
     * Crate new instance of YamlFileLoader
     * @param \Symfony\Component\Config\FileLocatorInterface $root_dir the directory into file exits.
     * @param string $file The fiel name to load
     * @param string $header the name of header for example parameters: or security: ...
     */

    /**
     * @param FileLocatorInterface $fileLocator the root file dir
     * @param string $file the file name with default config
     * @param string $environment the kernel config environment
     * @param string $header the header for yml file
     */
    public function __construct(FileLocatorInterface $fileLocator, $file, $environment, $header = 'parameters')
    {

        parent::__construct($fileLocator);
        $this->file = $file;
        $this->header = $header;
        $this->parameters = $this->load($file);
        $this->environment = $environment;
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string $type The resource type¡
     */
    /**
     * @param mixed $resource
     * @param null $type
     * @return array|bool|float|int|mixed|null|number|string
     * @throws \InvalidArgumentException
     */
    function load($resource, $type = null)
    {

        $ymlUserFiles = $this->getLocator()->locate($resource, '', false);
        if(count($ymlUserFiles) < 1){
            throw new \InvalidArgumentException("This $resource  not found ");
        }
        $collection = Yaml::parse($ymlUserFiles[0]);
        if(!empty($this->header)){
            if ($collection == null || !array_key_exists($this->header, $collection)){
                return array();
            };
            return $collection[$this->header];
        }
        return $collection;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string $type The resource type
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo(
            $resource,
            PATHINFO_EXTENSION
        );
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
        if(!array_key_exists(strtolower($parameterType), $this->parameters)){

            throw new ParameterNotFoundException($parameterType);
        }

        if(!array_key_exists(strtolower($parameterName), $this->parameters[strtolower($parameterType)])){
            throw new ParameterNotFoundException($parameterName);
        }

        return $this->parameters[strtolower($parameterType)][strtolower($parameterName)];
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
        return array_key_exists(strtolower($parameterType), $this->parameters) &&
        array_key_exists(strtolower($parameterName), $this->parameters[strtolower($parameterType)]);
    }

    /**
     * Clears all parameters.
     */
    public function clear()
    {
        $this->parameters = array();
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
        if($parameterType != null  && !$this->hasParameterType($parameterType) ){
            throw new ParameterNotFoundException($parameterType);
        }

        return $parameterType != null ? $this->parameters[strtolower($parameterType)] : $this->parameters;
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
        return array_key_exists(strtolower($parameterType), $this->parameters);
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

        foreach($this->parameters as $parameterTypeKey => $parameterTypeValues ){

            if(array_key_exists(strtolower($parameterTypeKey), $this->parameters) &&
                array_key_exists($name, $this->parameters[strtolower($parameterTypeKey)])){
                return $this->parameters[strtolower($parameterTypeKey)][$name];
            }
        }

        if($this->environment === 'dev'){
            throw new ParameterNotFoundException($name);
        }else{
            return null;
        }
    }
}