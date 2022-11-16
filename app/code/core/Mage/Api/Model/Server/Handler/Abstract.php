<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Api
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice default handler
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api_Model_Server_Handler_Abstract
{
    protected $_resourceSuffix = null;

    public function __construct()
    {
        set_error_handler([$this, 'handlePhpError'], E_ALL);
        Mage::app()->loadAreaPart(Mage_Core_Model_App_Area::AREA_ADMINHTML, Mage_Core_Model_App_Area::PART_EVENTS);
    }

    /**
     * @param int $errorCode
     * @param string $errorMessage
     * @param string $errorFile
     * @return bool
     */
    public function handlePhpError($errorCode, $errorMessage, $errorFile)
    {
        Mage::log($errorMessage . $errorFile);
        if (in_array($errorCode, [E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR])) {
            $this->_fault('internal');
        }
        return true;
    }

    /**
     * Retrieve webservice session
     *
     * @return Mage_Api_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('api/session');
    }

    /**
     * Retrieve webservice configuration
     *
     * @return Mage_Api_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('api/config');
    }

    /**
     * Retrieve webservice server
     *
     * @return Mage_Api_Model_Server
     */
    protected function _getServer()
    {
        return Mage::getSingleton('api/server');
    }

    /**
     * Start webservice session
     *
     * @param string $sessionId
     * @return Mage_Api_Model_Server_Handler_Abstract
     */
    protected function _startSession($sessionId = null)
    {
        $this->_getSession()->setSessionId($sessionId);
        $this->_getSession()->init('api', 'api');
        return $this;
    }

    /**
     * Check current user permission on resource and privilege
     *
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  bool
     */
    protected function _isAllowed($resource, $privilege = null)
    {
        return $this->_getSession()->isAllowed($resource, $privilege);
    }

    /**
     *  Check session expiration
     *
     *  @return  bool
     */
    protected function _isSessionExpired()
    {
        return $this->_getSession()->isSessionExpired();
    }

    /**
     * Dispatch webservice fault
     *
     * @param string $faultName
     * @param string $resourceName
     * @param string $customMessage
     */
    protected function _fault($faultName, $resourceName = null, $customMessage = null)
    {
        $faults = $this->_getConfig()->getFaults($resourceName);
        if (!isset($faults[$faultName]) && !is_null($resourceName)) {
            $this->_fault($faultName);
            return;
        } elseif (!isset($faults[$faultName])) {
            $this->_fault('unknown');
            return;
        }
        $this->_getServer()->getAdapter()->fault(
            $faults[$faultName]['code'],
            (is_null($customMessage) ? $faults[$faultName]['message'] : $customMessage)
        );
    }

    /**
     * Retrieve webservice fault as array
     *
     * @param string $faultName
     * @param string $resourceName
     * @param string $customMessage
     * @return array
     */
    protected function _faultAsArray($faultName, $resourceName = null, $customMessage = null)
    {
        $faults = $this->_getConfig()->getFaults($resourceName);
        if (!isset($faults[$faultName]) && !is_null($resourceName)) {
            return $this->_faultAsArray($faultName);
        } elseif (!isset($faults[$faultName])) {
            return $this->_faultAsArray('unknown');
        }

        return [
            'isFault'      => true,
            'faultCode'    => $faults[$faultName]['code'],
            'faultMessage' => (is_null($customMessage) ? $faults[$faultName]['message'] : $customMessage)
        ];
    }

    /**
     * Start web service session
     *
     * @return string
     */
    public function startSession()
    {
        $this->_startSession();
        return $this->_getSession()->getSessionId();
    }

    /**
     * End web service session
     *
     * @param string $sessionId
     * @return true
     */
    public function endSession($sessionId)
    {
        $this->_startSession($sessionId);
        $this->_getSession()->clear();
        return true;
    }

    /**
     * @param string $resource
     * @return string
     */
    protected function _prepareResourceModelName($resource)
    {
        if ($this->_resourceSuffix !== null) {
            return $resource . $this->_resourceSuffix;
        }
        return $resource;
    }

    /**
     * Login user and Retrieve session id
     *
     * @param string $username
     * @param string $apiKey
     * @return string
     */
    public function login($username, $apiKey = null)
    {
        if (empty($username) || empty($apiKey)) {
            return $this->_fault('invalid_request_param');
        }

        try {
            $this->_startSession();
            $this->_getSession()->login($username, $apiKey);
        } catch (Exception $e) {
            return $this->_fault('access_denied');
        }
        return $this->_getSession()->getSessionId();
    }

    /**
     * Call resource functionality
     *
     * @param string $sessionId
     * @param string $apiPath
     * @param array  $args
     * @return mixed
     */
    public function call($sessionId, $apiPath, $args = [])
    {
        $this->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            return $this->_fault('session_expired');
        }

        list($resourceName, $methodName) = explode('.', $apiPath);

        if (empty($resourceName) || empty($methodName)) {
            return $this->_fault('resource_path_invalid');
        }

        $resourcesAlias = $this->_getConfig()->getResourcesAlias();
        $resources      = $this->_getConfig()->getResources();
        if (isset($resourcesAlias->$resourceName)) {
            $resourceName = (string) $resourcesAlias->$resourceName;
        }

        if (!isset($resources->$resourceName)
            || !isset($resources->$resourceName->methods->$methodName)) {
            return $this->_fault('resource_path_invalid');
        }

        if (!isset($resources->$resourceName->public)
            && isset($resources->$resourceName->acl)
            && !$this->_isAllowed((string)$resources->$resourceName->acl)) {
            return $this->_fault('access_denied');
        }

        if (!isset($resources->$resourceName->methods->$methodName->public)
            && isset($resources->$resourceName->methods->$methodName->acl)
            && !$this->_isAllowed((string)$resources->$resourceName->methods->$methodName->acl)) {
            return $this->_fault('access_denied');
        }

        $methodInfo = $resources->$resourceName->methods->$methodName;

        try {
            $method = (isset($methodInfo->method) ? (string) $methodInfo->method : $methodName);

            $modelName = $this->_prepareResourceModelName((string) $resources->$resourceName->model);
            try {
                $model = Mage::getModel($modelName);
                if ($model instanceof Mage_Api_Model_Resource_Abstract) {
                    $model->setResourceConfig($resources->$resourceName);
                }
            } catch (Exception $e) {
                throw new Mage_Api_Exception('resource_path_not_callable');
            }

            if (method_exists($model, $method)) {
                $result = [];
                if (isset($methodInfo->arguments) && ((string)$methodInfo->arguments) == 'array') {
                    $result = $model->$method((is_array($args) ? $args : [$args]));
                } elseif (!is_array($args)) {
                    $result = $model->$method($args);
                } else {
                    $result = call_user_func_array([&$model, $method], $args);
                }
                return $this->processingMethodResult($result);
            } else {
                throw new Mage_Api_Exception('resource_path_not_callable');
            }
        } catch (Mage_Api_Exception $e) {
            return $this->_fault($e->getMessage(), $resourceName, $e->getCustomMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            return $this->_fault('internal', null, $e->getMessage());
        }
    }

    /**
     * Multiple calls of resource functionality
     *
     * @param string $sessionId
     * @param array $calls
     * @param array $options
     * @return array|void
     */
    public function multiCall($sessionId, array $calls = [], $options = [])
    {
        $this->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            return $this->_fault('session_expired');
        }

        $result = [];

        $resourcesAlias = $this->_getConfig()->getResourcesAlias();
        $resources      = $this->_getConfig()->getResources();

        foreach ($calls as $call) {
            if (!isset($call[0])) {
                $result[] = $this->_faultAsArray('resource_path_invalid');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            $apiPath = $call[0];
            $args    = $call[1] ?? [];

            list($resourceName, $methodName) = explode('.', $apiPath);

            if (empty($resourceName) || empty($methodName)) {
                $result[] = $this->_faultAsArray('resource_path_invalid');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            if (isset($resourcesAlias->$resourceName)) {
                $resourceName = (string) $resourcesAlias->$resourceName;
            }

            if (!isset($resources->$resourceName)
                || !isset($resources->$resourceName->methods->$methodName)) {
                $result[] = $this->_faultAsArray('resource_path_invalid');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            if (!isset($resources->$resourceName->public)
                && isset($resources->$resourceName->acl)
                && !$this->_isAllowed((string)$resources->$resourceName->acl)) {
                $result[] = $this->_faultAsArray('access_denied');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            if (!isset($resources->$resourceName->methods->$methodName->public)
                && isset($resources->$resourceName->methods->$methodName->acl)
                && !$this->_isAllowed((string)$resources->$resourceName->methods->$methodName->acl)) {
                $result[] = $this->_faultAsArray('access_denied');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            $methodInfo = $resources->$resourceName->methods->$methodName;

            try {
                $method = (isset($methodInfo->method) ? (string) $methodInfo->method : $methodName);

                $modelName = $this->_prepareResourceModelName((string) $resources->$resourceName->model);
                try {
                    $model = Mage::getModel($modelName);
                } catch (Exception $e) {
                    throw new Mage_Api_Exception('resource_path_not_callable');
                }

                if (method_exists($model, $method)) {
                    $callResult = [];
                    if (isset($methodInfo->arguments) && ((string)$methodInfo->arguments) == 'array') {
                        $callResult = $model->$method((is_array($args) ? $args : [$args]));
                    } elseif (!is_array($args)) {
                        $callResult = $model->$method($args);
                    } else {
                        $callResult = call_user_func_array([&$model, $method], $args);
                    }
                    $result[] = $this->processingMethodResult($callResult);
                } else {
                    throw new Mage_Api_Exception('resource_path_not_callable');
                }
            } catch (Mage_Api_Exception $e) {
                $result[] = $this->_faultAsArray($e->getMessage(), $resourceName, $e->getCustomMessage());
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            } catch (Exception $e) {
                Mage::logException($e);
                $result[] = $this->_faultAsArray('internal');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * List of available resources
     *
     * @param string $sessionId
     * @return array|void
     */
    public function resources($sessionId)
    {
        $this->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            return $this->_fault('session_expired');
        }

        $resources = [];

        $resourcesAlias = [];
        foreach ($this->_getConfig()->getResourcesAlias() as $alias => $resourceName) {
            $resourcesAlias[(string) $resourceName][] = $alias;
        }

        foreach ($this->_getConfig()->getResources() as $resourceName => $resource) {
            if (isset($resource->acl) && !$this->_isAllowed((string) $resource->acl)) {
                continue;
            }

            $methods = [];
            foreach ($resource->methods->children() as $methodName => $method) {
                if (isset($method->acl) && !$this->_isAllowed((string) $method->acl)) {
                    continue;
                }
                $methodAliases = [];
                if (isset($resourcesAlias[$resourceName])) {
                    foreach ($resourcesAlias[$resourceName] as $alias) {
                        $methodAliases[] =  $alias . '.' . $methodName;
                    }
                }

                $methods[] = [
                    'title'       => (string) $method->title,
                    'description' => (isset($method->description) ? (string)$method->description : null),
                    'path'        => $resourceName . '.' . $methodName,
                    'name'        => $methodName,
                    'aliases'     => $methodAliases
                ];
            }

            if (count($methods) == 0) {
                continue;
            }

            $resources[] = [
                'title'       => (string) $resource->title,
                'description' => (isset($resource->description) ? (string)$resource->description : null),
                'name'        => $resourceName,
                'aliases'     => $resourcesAlias[$resourceName] ?? [],
                'methods'     => $methods
            ];
        }

        return $resources;
    }

    /**
     * List of resource faults
     *
     * @param string $sessionId
     * @param string $resourceName
     * @return array|void
     */
    public function resourceFaults($sessionId, $resourceName)
    {
        $this->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            return $this->_fault('session_expired');
        }

        $resourcesAlias = $this->_getConfig()->getResourcesAlias();
        $resources      = $this->_getConfig()->getResources();

        if (isset($resourcesAlias->$resourceName)) {
            $resourceName = (string) $resourcesAlias->$resourceName;
        }

        if (empty($resourceName)
            || !isset($resources->$resourceName)) {
            return $this->_fault('resource_path_invalid');
        }

        if (isset($resources->$resourceName->acl)
            && !$this->_isAllowed((string)$resources->$resourceName->acl)) {
            return $this->_fault('access_denied');
        }

        return array_values($this->_getConfig()->getFaults($resourceName));
    }

    /**
     * List of global faults
     *
     * @param  string $sessionId
     * @return array
     */
    public function globalFaults($sessionId)
    {
        $this->_startSession($sessionId);
        return array_values($this->_getConfig()->getFaults());
    }

    /**
     * Prepare Api data for XML exporting
     * See allowed characters in XML:
     * @link http://www.w3.org/TR/2000/REC-xml-20001006#NT-Char
     *
     * @param mixed $result
     * @return mixed
     */
    public function processingMethodResult($result)
    {
        if (is_null($result) || is_bool($result) || is_numeric($result) || is_object($result)) {
            return $result;
        } elseif (is_array($result)) {
            foreach ($result as $key => $value) {
                $result[$key] = $this->processingMethodResult($value);
            }
        } else {
            $result = $this->processingRow($result);
        }

        return $result;
    }

    /**
     * Prepare Api row data for XML exporting
     * Convert not allowed symbol to numeric character reference
     *
     * @param mixed $row
     * @return mixed
     */
    public function processingRow($row)
    {
        $row = preg_replace_callback(
            '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u',
            function ($matches) {
                return '&#' . Mage::helper('core/string')->uniOrd($matches[0]) . ';';
            },
            $row
        );
        return $row;
    }
}
