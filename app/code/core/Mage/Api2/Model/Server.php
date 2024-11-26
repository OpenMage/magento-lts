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
 * API2 Server
 *
 * @category   Mage
 * @package    Mage_Api2
 */
class Mage_Api2_Model_Server
{
    /**
     * Api2 REST type
     */
    public const API_TYPE_REST = 'rest';

    /**
     * HTTP Response Codes
     */
    public const HTTP_OK                 = 200;
    public const HTTP_CREATED            = 201;
    public const HTTP_MULTI_STATUS       = 207;
    public const HTTP_BAD_REQUEST        = 400;
    public const HTTP_UNAUTHORIZED       = 401;
    public const HTTP_FORBIDDEN          = 403;
    public const HTTP_NOT_FOUND          = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE     = 406;
    public const HTTP_INTERNAL_ERROR     = 500;

    /**
     * List of api types
     *
     * @var array
     */
    protected static $_apiTypes = [self::API_TYPE_REST];

    /**
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_authUser;

    /**
     * Run server
     */
    public function run()
    {
        // can not use response object case
        try {
            /** @var Mage_Api2_Model_Response $response */
            $response = Mage::getSingleton('api2/response');
        } catch (Exception $e) {
            Mage::logException($e);

            if (!headers_sent()) {
                header('HTTP/1.1 ' . self::HTTP_INTERNAL_ERROR);
            }
            echo 'Service temporary unavailable';
            return;
        }
        // can not render errors case
        try {
            /** @var Mage_Api2_Model_Request $request */
            $request = Mage::getSingleton('api2/request');
            /** @var Mage_Api2_Model_Renderer_Interface $renderer */
            $renderer = Mage_Api2_Model_Renderer::factory($request->getAcceptTypes());
        } catch (Exception $e) {
            Mage::logException($e);

            $response->setHttpResponseCode(self::HTTP_INTERNAL_ERROR)
                ->setBody('Service temporary unavailable')
                ->sendResponse();
            return;
        }
        // default case
        try {
            $apiUser = $this->_authenticate($request);

            $this->_route($request)
                ->_allow($request, $apiUser)
                ->_dispatch($request, $response, $apiUser);

            if ($response->getHttpResponseCode() == self::HTTP_CREATED) {
                // TODO: Re-factor this after _renderException refactoring
                throw new Mage_Api2_Exception('Resource was partially created', self::HTTP_CREATED);
            }
            //NOTE: At this moment Renderer already could have some content rendered, so we should replace it
            if ($response->isException()) {
                throw new Mage_Api2_Exception('Unhandled simple errors.', self::HTTP_INTERNAL_ERROR);
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_renderException($e, $renderer, $response);
        }

        $response->sendResponse();
    }

    /**
     * Make internal call to api
     *
     * @throws Mage_Api2_Exception
     */
    public function internalCall(Mage_Api2_Model_Request $request, Mage_Api2_Model_Response $response)
    {
        $apiUser = $this->_getAuthUser();
        $this->_route($request)
            ->_allow($request, $apiUser)
            ->_dispatch($request, $response, $apiUser);
    }

    /**
     * Authenticate user
     *
     * @throws Exception
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    protected function _authenticate(Mage_Api2_Model_Request $request)
    {
        /** @var Mage_Api2_Model_Auth $authManager */
        $authManager = Mage::getModel('api2/auth');

        $this->_setAuthUser($authManager->authenticate($request));
        return $this->_getAuthUser();
    }

    /**
     * Set auth user
     *
     * @throws Exception
     * @return $this
     */
    protected function _setAuthUser(Mage_Api2_Model_Auth_User_Abstract $authUser)
    {
        $this->_authUser = $authUser;
        return $this;
    }

    /**
     * Retrieve existing auth user
     *
     * @throws Exception
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    protected function _getAuthUser()
    {
        if (!$this->_authUser) {
            throw new Exception('Mage_Api2_Model_Server::internalCall() seems to be executed '
                . 'before Mage_Api2_Model_Server::run()');
        }
        return $this->_authUser;
    }

    /**
     * Set all routes of the given api type to Route object
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @return $this
     */
    protected function _route(Mage_Api2_Model_Request $request)
    {
        /** @var Mage_Api2_Model_Router $router */
        $router = Mage::getModel('api2/router');

        $router->routeApiType($request, true)
            ->setRoutes($this->_getConfig()->getRoutes($request->getApiType()))
            ->route($request);

        return $this;
    }

    /**
     * Global ACL processing
     *
     * @return $this
     * @throws Mage_Api2_Exception
     */
    protected function _allow(Mage_Api2_Model_Request $request, Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        /** @var Mage_Api2_Model_Acl_Global $globalAcl */
        $globalAcl = Mage::getModel('api2/acl_global');

        if (!$globalAcl->isAllowed($apiUser, $request->getResourceType(), $request->getOperation())) {
            throw new Mage_Api2_Exception('Access denied', self::HTTP_FORBIDDEN);
        }
        return $this;
    }

    /**
     * Load class file, instantiate resource class, set parameters to the instance, run resource internal dispatch
     * method
     *
     * @return $this
     */
    protected function _dispatch(
        Mage_Api2_Model_Request $request,
        Mage_Api2_Model_Response $response,
        Mage_Api2_Model_Auth_User_Abstract $apiUser
    ) {
        /** @var Mage_Api2_Model_Dispatcher $dispatcher */
        $dispatcher = Mage::getModel('api2/dispatcher');
        $dispatcher->setApiUser($apiUser)->dispatch($request, $response);

        return $this;
    }

    /**
     * Get api2 config instance
     *
     * @return Mage_Api2_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getModel('api2/config');
    }

    /**
     * Process thrown exception
     * Generate and set HTTP response code, error message to Response object
     *
     * @return $this
     */
    protected function _renderException(
        Exception $exception,
        Mage_Api2_Model_Renderer_Interface $renderer,
        Mage_Api2_Model_Response $response
    ) {
        if ($exception instanceof Mage_Api2_Exception && $exception->getCode()) {
            $httpCode = $exception->getCode();
        } else {
            $httpCode = self::HTTP_INTERNAL_ERROR;
        }
        try {
            //add last error to stack
            $response->setException($exception);

            $messages = [];

            /** @var Exception $exception */
            foreach ($response->getException() as $exception) {
                $message = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];

                if (Mage::getIsDeveloperMode()) {
                    $message['trace'] = $exception->getTraceAsString();
                }
                $messages['messages']['error'][] = $message;
            }
            //set HTTP Code of last error, Content-Type and Body
            $response->setBody($renderer->render($messages));
            $response->setHeader('Content-Type', sprintf(
                '%s; charset=%s',
                $renderer->getMimeType(),
                Mage_Api2_Model_Response::RESPONSE_CHARSET
            ));
        } catch (Exception $e) {
            //tunnelling of 406(Not acceptable) error
            $httpCode = $e->getCode() == self::HTTP_NOT_ACCEPTABLE    //$e->getCode() can result in one more loop
                    ? self::HTTP_NOT_ACCEPTABLE                      // of try..catch
                    : self::HTTP_INTERNAL_ERROR;

            //if error appeared in "error rendering" process then show it in plain text
            $response->setBody($e->getMessage());
            $response->setHeader('Content-Type', 'text/plain; charset=' . Mage_Api2_Model_Response::RESPONSE_CHARSET);
        }
        $response->setHttpResponseCode($httpCode);

        return $this;
    }

    /**
     * Retrieve api types
     *
     * @return array
     */
    public static function getApiTypes()
    {
        return self::$_apiTypes;
    }
}
