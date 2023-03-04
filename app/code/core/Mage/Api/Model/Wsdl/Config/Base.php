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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wsdl base config
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Wsdl_Config_Base extends Varien_Simplexml_Config
{
    protected $_handler = '';

    /**
     * @var Varien_Object
     */
    protected $_wsdlVariables = null;

    protected $_loadedFiles = [];

    /**
     * @inheritDoc
     */
    public function __construct($sourceData = null)
    {
        $this->_elementClass = 'Mage_Api_Model_Wsdl_Config_Element';

        // remove wsdl parameter from query
        $queryParams = Mage::app()->getRequest()->getQuery();
        unset($queryParams['wsdl']);

        // set up default WSDL template variables
        $this->_wsdlVariables = new Varien_Object(
            [
                'name' => 'OpenMage',
                'url'  => Mage::helper('api')->getServiceUrl('*/*/*', ['_query' => $queryParams], true)
            ]
        );
        parent::__construct($sourceData);
    }

    /**
     * Set handler
     *
     * @param string $handler
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
        return $this;
    }

    /**
     * Get handler
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->_handler;
    }

    /**
     * Processing file data
     *
     * @param string $text
     * @return string
     */
    public function processFileData($text)
    {
        /** @var Mage_Core_Model_Email_Template_Filter $template */
        $template = Mage::getModel('core/email_template_filter');

        $this->_wsdlVariables->setHandler($this->getHandler());

        $template->setVariables(['wsdl' => $this->_wsdlVariables]);

        return $template->filter($text);
    }

    /**
     * @param string $file
     * @return $this
     */
    public function addLoadedFile($file)
    {
        if (!in_array($file, $this->_loadedFiles)) {
            $this->_loadedFiles[] = $file;
        }
        return $this;
    }

    /**
     * @param string $file
     * @return $this|false
     */
    public function loadFile($file)
    {
        if (in_array($file, $this->_loadedFiles)) {
            return false;
        }
        $res = parent::loadFile($file);
        if ($res) {
            $this->addLoadedFile($file);
        }
        return $this;
    }

    /**
     * Set variable to be used in WSDL template processing
     *
     * @param string $key Varible key
     * @param string $value Variable value
     * @return $this
     */
    public function setWsdlVariable($key, $value)
    {
        $this->_wsdlVariables->setData($key, $value);

        return $this;
    }
}
