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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice api2 dispatcher model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Dispatcher
{
    /**
     * Template for retrieve resource class name
     */
    public const RESOURCE_CLASS_TEMPLATE = ':resource_:api_:user_v:version';

    /**
     * API User object
     *
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_apiUser;

    /**
     * Instantiate resource class, set parameters to the instance, run resource internal dispatch method
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @return $this
     * @throws Mage_Api2_Exception
     */
    public function dispatch(Mage_Api2_Model_Request $request, Mage_Api2_Model_Response $response)
    {
        if (!$request->getModel() || !$request->getApiType()) {
            throw new Mage_Api2_Exception(
                'Request does not contains all necessary data',
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST
            );
        }
        $model = self::loadResourceModel(
            $request->getModel(),
            $request->getApiType(),
            $this->getApiUser()->getType(),
            $this->getVersion($request->getResourceType(), $request->getVersion())
        );

        $model->setRequest($request);
        $model->setResponse($response);
        $model->setApiUser($this->getApiUser());

        $model->dispatch();

        return $this;
    }

    /**
     * Pack resource model class path from components and try to load it
     *
     * @param string $model
     * @param string $apiType API type
     * @param string $userType API User type (e.g. admin, customer, guest)
     * @param int $version Requested version
     * @return Mage_Api2_Model_Resource
     * @throws Mage_Api2_Exception
     */
    public static function loadResourceModel($model, $apiType, $userType, $version)
    {
        $class = strtr(
            self::RESOURCE_CLASS_TEMPLATE,
            [':resource' => $model, ':api' => $apiType, ':user' => $userType, ':version' => $version]
        );

        try {
            /** @var Mage_Api2_Model_Resource $modelObj */
            $modelObj = Mage::getModel($class);
        } catch (Exception $e) {
            // getModel() throws exception when in application is in development mode - skip it to next check
        }
        if (empty($modelObj) || !$modelObj instanceof Mage_Api2_Model_Resource) {
            throw new Mage_Api2_Exception('Resource not found', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        return $modelObj;
    }

    /**
     * Set API user object
     *
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @return $this
     */
    public function setApiUser(Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        $this->_apiUser = $apiUser;

        return $this;
    }

    /**
     * Get API user object
     *
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    public function getApiUser()
    {
        if (!$this->_apiUser) {
            throw new Exception('API user is not set.');
        }

        return $this->_apiUser;
    }

    /**
     * Get correct version of the resource model
     *
     * @param string $resourceType
     * @param string|bool $requestedVersion
     * @return int
     * @throws Mage_Api2_Exception
     */
    public function getVersion($resourceType, $requestedVersion)
    {
        if ($requestedVersion !== false && !preg_match('/^[1-9]\d*$/', $requestedVersion)) {
            throw new Mage_Api2_Exception(
                sprintf('Invalid version "%s" requested.', htmlspecialchars($requestedVersion)),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST
            );
        }
        return $this->getConfig()->getResourceLastVersion($resourceType, $requestedVersion);
    }

    /**
     * Get config
     *
     * @return Mage_Api2_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel('api2/config');
    }
}
