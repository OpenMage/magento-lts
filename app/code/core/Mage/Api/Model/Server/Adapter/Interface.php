<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Api
 */

/**
 * Web service api server interface
 *
 * @category   Mage
 * @package    Mage_Api
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
