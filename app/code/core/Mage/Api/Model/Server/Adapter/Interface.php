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
 * Web service api server interface
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Api_Model_Server_Adapter_Interface
{
    /**
     * Set handler class name for webservice
     *
     * @param string $handler
     * @return Mage_Api_Model_Server_Adapter_Interface
     */
    public function setHandler($handler);

    /**
     * Retrieve handler class name for webservice
     *
     * @return string
     */
    public function getHandler();

    /**
     * Set webservice api controller
     *
     * @param Mage_Api_Controller_Action $controller
     * @return Mage_Api_Model_Server_Adapter_Interface
     */
    public function setController(Mage_Api_Controller_Action $controller);

    /**
     * Retrieve webservice api controller
     *
     * @return Mage_Api_Controller_Action
     */
    public function getController();

    /**
     * Run webservice
     *
     * @return Mage_Api_Model_Server_Adapter_Interface
     */
    public function run();

    /**
     * Dispatch webservice fault
     *
     * @param int $code
     * @param string $message
     */
    public function fault($code, $message);
}
