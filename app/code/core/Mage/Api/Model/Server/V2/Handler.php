<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservices server handler v2
 *
 * @category   Mage
 * @package    Mage_Api
 */
class Mage_Api_Model_Server_V2_Handler extends Mage_Api_Model_Server_Handler_Abstract
{
    protected $_resourceSuffix = '_v2';

    /**
     * Interceptor for all interfaces
     *
     * @param string $function
     * @param array $args
     * @return mixed
     */

    public function __call($function, $args)
    {
        $sessionId = array_shift($args);
        $apiKey = '';
        $nodes = Mage::getSingleton('api/config')->getNode('v2/resources_function_prefix')->children();
        foreach ($nodes as $resource => $prefix) {
            $prefix = $prefix->asArray();
            if (str_contains($function, $prefix)) {
                $method = substr($function, strlen($prefix));
                $apiKey = $resource . '.' . strtolower($method[0]) . substr($method, 1);
            }
        }
        return $this->call($sessionId, $apiKey, $args);
    }
}
