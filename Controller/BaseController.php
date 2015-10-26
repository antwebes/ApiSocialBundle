<?php
/**
 * Created by PhpStorm.
 * User: ping
 * Date: 5/06/15
 * Time: 10:24
 */

namespace Ant\Bundle\ApiSocialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BaseController extends Controller
{
    /**
     * a partir de channelType=2,name=aaaaa los separamos y añadimos los filtros
     * @param unknown $filterString
     * @return multitype:
     */
    public function getFilters($filterString)
    {
        $delimiters = array(',','=');

        $filters = $this->multiexplode($delimiters, $filterString);


        return $filters;
    }

    /**
     *
     * @param array $delimiters
     * @param string $string string to explode
     * @return multitype:
     * @throws BadRequestHttpException
     */
    public function multiexplode(array $delimiters, $string)
    {
        $type = explode($delimiters[0], $string);
        foreach ($type as $pair){
            if (!strpos($pair, $delimiters[1])){
                throw new BadRequestHttpException('invalid_request');
            }
            list($k, $v) = explode($delimiters[1], $pair);
            $result[$k] = $v;
        }
        return $result;
    }

    /**
     * Tries to find an entity or throws an NotFoundHttpException
     * @param $serviceName
     * @param $finder
     * @param $params
     * @param string $message
     * @return mixed
     * @throws NotFountHttpException
     */
    protected function findApiEntityOrThrowNotFoundException($serviceName, $finder, $params, $message = 'Not Found')
    {
        $manager = $this->get($serviceName);

        // if params is not an array make sure it is to pass it to call_user_func_array
        if(!is_array($params)){
            $params = array($params);
        }

        // call the service
        $entity = call_user_func_array(array($manager, $finder), $params);

        if($entity === null){
            throw $this->createNotFoundException($message);
        }

        return $entity;
    }
} 