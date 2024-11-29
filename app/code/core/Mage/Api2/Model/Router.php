<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api2 router model
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Router
{
    /**
     * Routes which are stored in module config files api2.xml
     *
     * @var array
     */
    protected $_routes = [];

    /**
     * Set routes
     *
     * @return $this
     */
    public function setRoutes(array $routes)
    {
        $this->_routes = $routes;

        return $this;
    }

    /**
     * Get routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Route the Request, the only responsibility of the class
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @return Mage_Api2_Model_Request
     * @throws Mage_Api2_Exception
     */
    public function route(Mage_Api2_Model_Request $request)
    {
        $isMatched = false;

        /** @var Mage_Api2_Model_Route_Interface $route */
        foreach ($this->getRoutes() as $route) {
            if ($params = $route->match($request)) {
                $request->setParams($params);
                $isMatched = true;
                break;
            }
        }
        if (!$isMatched) {
            throw new Mage_Api2_Exception('Request does not match any route.', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        if (!$request->getResourceType() || !$request->getModel()) {
            throw new Mage_Api2_Exception(
                'Matched resource is not properly set.',
                Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
            );
        }
        return $request;
    }

    /**
     * Set API type to request as a result of one pass route
     *
     * @param bool $trimApiTypePath OPTIONAL If TRUE - /api/:api_type part of request path info will be trimmed
     * @return $this
     * @throws Mage_Api2_Exception
     */
    public function routeApiType(Mage_Api2_Model_Request $request, $trimApiTypePath = true)
    {
        /** @var Mage_Api2_Model_Route_ApiType $apiTypeRoute */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        if (!($apiTypeMatch = $apiTypeRoute->match($request, true))) {
            throw new Mage_Api2_Exception('Request does not match type route.', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        // Trim matched URI path for next routes
        if ($trimApiTypePath) {
            $matchedPathLength = strlen('/' . ltrim($apiTypeRoute->getMatchedPath(), '/'));

            $request->setPathInfo(substr($request->getPathInfo(), $matchedPathLength));
        }
        $request->setParam('api_type', $apiTypeMatch['api_type']);

        return $this;
    }
}
