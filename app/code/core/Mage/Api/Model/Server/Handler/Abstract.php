<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api
 */

/**
 * Webservice default handler
 *
 * @package    Mage_Api
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
    public function handlePhpError($errorCode, $errorMessage, $errorFile, $errLine)
    {
        Mage::log($errorMessage . ' in ' . $errorFile . ' on line ' . $errLine, Zend_Log::ERR);
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
     * Allow insta-login via HTTP Basic Auth
     *
     * @param stdClass|string|null $sessionId
     * @return $this
     * @SuppressWarnings("PHPMD.Superglobals")
     */
    protected function _instaLogin(&$sessionId)
    {
        if ($sessionId === null && !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
            $this->_getSession()->setIsInstaLogin();
            $sessionId = $this->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
        }

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
            (is_null($customMessage) ? $faults[$faultName]['message'] : $customMessage),
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
            'faultMessage' => (is_null($customMessage) ? $faults[$faultName]['message'] : $customMessage),
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
     * @return stdClass|string|void
     */
    public function login($username, $apiKey = null)
    {
        if (empty($username) || empty($apiKey)) {
            $this->_fault('invalid_request_param');
            return;
        }

        $username = new Mage_Core_Model_Security_Obfuscated($username);
        $apiKey   = new Mage_Core_Model_Security_Obfuscated($apiKey);

        try {
            $this->_startSession();
            $this->_getSession()->login($username, $apiKey);
        } catch (Exception) {
            $this->_fault('access_denied');
            return;
        }

        return $this->_getSession()->getSessionId();
    }

    /**
     * Call resource functionality
     *
     * @param string $sessionId
     * @param string $apiPath
     * @param array  $args
     * @return mixed|void
     */
    public function call($sessionId, $apiPath, $args = [])
    {
        $this->_instaLogin($sessionId)
            ->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            $this->_fault('session_expired');
            return;
        }

        [$resourceName, $methodName] = explode('.', $apiPath);

        if (empty($resourceName) || empty($methodName)) {
            $this->_fault('resource_path_invalid');
            return;
        }

        $resourcesAlias = $this->_getConfig()->getResourcesAlias();
        $resources      = $this->_getConfig()->getResources();
        if (isset($resourcesAlias->$resourceName)) {
            $resourceName = (string) $resourcesAlias->$resourceName;
        }

        if (!isset($resources->$resourceName)
            || !isset($resources->$resourceName->methods->$methodName)
        ) {
            $this->_fault('resource_path_invalid');
            return;
        }

        if (!isset($resources->$resourceName->public)
            && isset($resources->$resourceName->acl)
            && !$this->_isAllowed((string) $resources->$resourceName->acl)
        ) {
            $this->_fault('access_denied');
            return;
        }

        if (!isset($resources->$resourceName->methods->$methodName->public)
            && isset($resources->$resourceName->methods->$methodName->acl)
            && !$this->_isAllowed((string) $resources->$resourceName->methods->$methodName->acl)
        ) {
            $this->_fault('access_denied');
            return;
        }

        $methodInfo = $resources->$resourceName->methods->$methodName;
        $method = (isset($methodInfo->method) ? (string) $methodInfo->method : $methodName);

        if (!isset($resources->$resourceName->model)) {
            throw new Mage_Api_Exception('resource_path_not_callable');
        }

        try {
            $modelName = $this->_prepareResourceModelName((string) $resources->$resourceName->model);
            try {
                $model = Mage::getModel($modelName);
                if ($model instanceof Mage_Api_Model_Resource_Abstract) {
                    $model->setResourceConfig($resources->$resourceName);
                }
            } catch (Exception) {
                throw new Mage_Api_Exception('resource_path_not_callable');
            }

            if (method_exists($model, $method)) {
                if (isset($methodInfo->arguments) && ((string) $methodInfo->arguments) == 'array') {
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
            $this->_fault($e->getMessage(), $resourceName, $e->getCustomMessage());
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_fault('internal', null, $e->getMessage());
            return;
        }
    }

    /**
     * Multiple calls of resource functionality
     *
     * @param string $sessionId
     * @param array $options
     * @return array|void
     */
    public function multiCall($sessionId, array $calls = [], $options = [])
    {
        $this->_instaLogin($sessionId)
            ->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            $this->_fault('session_expired');
            return;
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

            [$resourceName, $methodName] = explode('.', $apiPath);

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
                || !isset($resources->$resourceName->methods->$methodName)
            ) {
                $result[] = $this->_faultAsArray('resource_path_invalid');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            if (!isset($resources->$resourceName->public)
                && isset($resources->$resourceName->acl)
                && !$this->_isAllowed((string) $resources->$resourceName->acl)
            ) {
                $result[] = $this->_faultAsArray('access_denied');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            if (!isset($resources->$resourceName->methods->$methodName->public)
                && isset($resources->$resourceName->methods->$methodName->acl)
                && !$this->_isAllowed((string) $resources->$resourceName->methods->$methodName->acl)
            ) {
                $result[] = $this->_faultAsArray('access_denied');
                if (isset($options['break']) && $options['break'] == 1) {
                    break;
                } else {
                    continue;
                }
            }

            $methodInfo = $resources->$resourceName->methods->$methodName;
            $method = (isset($methodInfo->method) ? (string) $methodInfo->method : $methodName);

            if (!isset($resources->$resourceName->model)) {
                throw new Mage_Api_Exception('resource_path_not_callable');
            }

            try {
                $modelName = $this->_prepareResourceModelName((string) $resources->$resourceName->model);

                try {
                    $model = Mage::getModel($modelName);
                } catch (Exception) {
                    throw new Mage_Api_Exception('resource_path_not_callable');
                }

                if (method_exists($model, $method)) {
                    if (isset($methodInfo->arguments) && ((string) $methodInfo->arguments) == 'array') {
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
        $this->_instaLogin($sessionId)
            ->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            $this->_fault('session_expired');
            return;
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
                    'description' => (isset($method->description) ? (string) $method->description : null),
                    'path'        => $resourceName . '.' . $methodName,
                    'name'        => $methodName,
                    'aliases'     => $methodAliases,
                ];
            }

            if (count($methods) == 0) {
                continue;
            }

            $resources[] = [
                'title'       => (string) $resource->title,
                'description' => (isset($resource->description) ? (string) $resource->description : null),
                'name'        => $resourceName,
                'aliases'     => $resourcesAlias[$resourceName] ?? [],
                'methods'     => $methods,
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
        $this->_instaLogin($sessionId)
            ->_startSession($sessionId);

        if (!$this->_getSession()->isLoggedIn($sessionId)) {
            $this->_fault('session_expired');
            return;
        }

        $resourcesAlias = $this->_getConfig()->getResourcesAlias();
        $resources      = $this->_getConfig()->getResources();

        if (isset($resourcesAlias->$resourceName)) {
            $resourceName = (string) $resourcesAlias->$resourceName;
        }

        if (empty($resourceName)
            || !isset($resources->$resourceName)
        ) {
            $this->_fault('resource_path_invalid');
            return;
        }

        if (isset($resources->$resourceName->acl)
            && !$this->_isAllowed((string) $resources->$resourceName->acl)
        ) {
            $this->_fault('access_denied');
            return;
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
        $this->_instaLogin($sessionId)
            ->_startSession($sessionId);
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
        return preg_replace_callback(
            '/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u',
            function ($matches) {
                return '&#' . Mage::helper('core/string')->uniOrd($matches[0]) . ';';
            },
            $row,
        );
    }
}
