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
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API Request model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request extends Zend_Controller_Request_Http
{
    /**
     * Character set which must be used in request
     */
    public const REQUEST_CHARSET = 'utf-8';

    /**#@+
     * Name of query ($_GET) parameters to use in navigation and so on
     */
    public const QUERY_PARAM_REQ_ATTRS   = 'attrs';
    public const QUERY_PARAM_PAGE_NUM    = 'page';
    public const QUERY_PARAM_PAGE_SIZE   = 'limit';
    public const QUERY_PARAM_ORDER_FIELD = 'order';
    public const QUERY_PARAM_ORDER_DIR   = 'dir';
    public const QUERY_PARAM_FILTER      = 'filter';
    /**#@- */

    /**
     * Interpreter adapter
     *
     * @var Mage_Api2_Model_Request_Interpreter_Interface
     */
    protected $_interpreter;

    /**
     * Body params
     *
     * @var array
     */
    protected $_bodyParams;

    /**
     * Constructor
     *
     * If a $uri is passed, the object will attempt to populate itself using
     * that information.
     * Override parent class to allow object instance get via Mage::getSingleton()
     *
     * @param string|Zend_Uri $uri
     */
    public function __construct($uri = null)
    {
        parent::__construct($uri ? $uri : null);
    }

    /**
     * Get request interpreter
     *
     * @return Mage_Api2_Model_Request_Interpreter_Interface
     */
    protected function _getInterpreter()
    {
        if ($this->_interpreter === null) {
            $this->_interpreter = Mage_Api2_Model_Request_Interpreter::factory($this->getContentType());
        }
        return $this->_interpreter;
    }

    /**
     * Retrieve accept types understandable by requester in a form of array sorted by quality descending
     *
     * @return array
     */
    public function getAcceptTypes()
    {
        $qualityToTypes = [];
        $orderedTypes   = [];

        foreach (preg_split('/,\s*/', $this->getHeader('Accept')) as $definition) {
            $typeWithQ = explode(';', $definition);
            $mimeType  = trim(array_shift($typeWithQ));

            // check MIME type validity
            if (!preg_match('~^([0-9a-z*+\-]+)(?:/([0-9a-z*+\-\.]+))?$~i', $mimeType)) {
                continue;
            }
            $quality = '1.0'; // default value for quality

            if ($typeWithQ) {
                $qAndValue = explode('=', $typeWithQ[0]);

                if (count($qAndValue) == 2) {
                    $quality = $qAndValue[1];
                }
            }
            $qualityToTypes[$quality][$mimeType] = true;
        }
        krsort($qualityToTypes);

        foreach ($qualityToTypes as $typeList) {
            $orderedTypes += $typeList;
        }
        return array_keys($orderedTypes);
    }

    /**
     * Get api type from Request
     *
     * @return string
     */
    public function getApiType()
    {
        // getParam() is not used to avoid parameter fetch from $_GET or $_POST
        return $this->_params['api_type'] ?? null;
    }

    /**
     * Fetch data from HTTP Request body
     *
     * @return array
     */
    public function getBodyParams()
    {
        if ($this->_bodyParams == null) {
            $this->_bodyParams = $this->_getInterpreter()->interpret((string)$this->getRawBody());
        }
        return $this->_bodyParams;
    }

    /**
     * Get Content-Type of request
     *
     * @return string
     * @throws Mage_Api2_Exception
     */
    public function getContentType()
    {
        $headerValue = $this->getHeader('Content-Type');

        if (!$headerValue) {
            throw new Mage_Api2_Exception('Content-Type header is empty', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!preg_match('~^([a-z\d/\-+.]+)(?:; *charset=(.+))?$~Ui', $headerValue, $matches)) {
            throw new Mage_Api2_Exception('Invalid Content-Type header', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // request encoding check if it is specified in header
        if (isset($matches[2]) && self::REQUEST_CHARSET != strtolower($matches[2])) {
            throw new Mage_Api2_Exception(
                'UTF-8 is the only supported charset',
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST
            );
        }
        return $matches[1];
    }

    /**
     * Get filter settings passed by API user
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->getQuery(self::QUERY_PARAM_FILTER);
    }

    /**
     * Get resource model class name
     *
     * @return string|null
     */
    public function getModel()
    {
        // getParam() is not used to avoid parameter fetch from $_GET or $_POST
        return $this->_params['model'] ?? null;
    }

    /**
     * Retrieve one of CRUD operation dependent on HTTP method
     *
     * @return string
     * @throws Mage_Api2_Exception
     */
    public function getOperation()
    {
        if (!$this->isGet() && !$this->isPost() && !$this->isPut() && !$this->isDelete()) {
            throw new Mage_Api2_Exception('Invalid request method', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // Map HTTP methods to classic CRUD verbs
        $operationByMethod = [
            'GET'    => Mage_Api2_Model_Resource::OPERATION_RETRIEVE,
            'POST'   => Mage_Api2_Model_Resource::OPERATION_CREATE,
            'PUT'    => Mage_Api2_Model_Resource::OPERATION_UPDATE,
            'DELETE' => Mage_Api2_Model_Resource::OPERATION_DELETE
        ];

        return $operationByMethod[$this->getMethod()];
    }

    /**
     * Get sort order direction requested by API user
     *
     * @return mixed
     */
    public function getOrderDirection()
    {
        return $this->getQuery(self::QUERY_PARAM_ORDER_DIR);
    }

    /**
     * Get sort order field requested by API user
     *
     * @return mixed
     */
    public function getOrderField()
    {
        return $this->getQuery(self::QUERY_PARAM_ORDER_FIELD);
    }

    /**
     * Retrieve page number requested by API user
     *
     * @return mixed
     */
    public function getPageNumber()
    {
        return $this->getQuery(self::QUERY_PARAM_PAGE_NUM);
    }

    /**
     * Retrieve page size requested by API user
     *
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->getQuery(self::QUERY_PARAM_PAGE_SIZE);
    }

    /**
     * Get an array of attribute codes requested by API user
     *
     * @return array
     */
    public function getRequestedAttributes()
    {
        $include = $this->getQuery(self::QUERY_PARAM_REQ_ATTRS, []);

        //transform comma-separated list
        if (!is_array($include)) {
            $include = explode(',', $include);
        }
        return array_map('trim', $include);
    }

    /**
     * Retrieve resource type
     *
     * @return string
     */
    public function getResourceType()
    {
        // getParam() is not used to avoid parameter fetch from $_GET or $_POST
        return $this->_params['type'] ?? null;
    }

    /**
     * Get Version header from headers
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->getHeader('Version');
    }

    /**
     * Retrieve action type
     *
     * @return string|null
     */
    public function getActionType()
    {
        // getParam() is not used to avoid parameter fetch from $_GET or $_POST
        return $this->_params['action_type'] ?? null;
    }

    /**
     * It checks if the array in the request body is an associative one.
     * It is required for definition of the dynamic aaction type (multi or single)
     *
     * @return bool
     */
    public function isAssocArrayInRequestBody()
    {
        $params = $this->getBodyParams();
        if (count($params)) {
            $keys = array_keys($params);
            return !is_numeric($keys[0]);
        }
        return false;
    }
}
