<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservices server handler v2
 *
 * @package    Mage_Api
 */
class Mage_Api_Model_Server_V2_Handler extends Mage_Api_Model_Server_Handler_Abstract
{
    protected $_resourceSuffix = '_v2';

    /**
     * Interceptor for all interfaces
     *
     * @param  string $function
     * @param  array  $args
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
