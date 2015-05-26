<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPal online logo with additional options
 */
class Mage_Paypal_Block_Bml_Banners extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_section;

    /**
     * @var int
     */
    protected $_position;

    /**
     * @param string $section
     */
    public function setSection($section = '')
    {
        $this->_section = $section;
    }

    /**
     * @param int $position
     */
    public function setPosition($position = 0)
    {
        $this->_position = $position;
    }

    /**
     * Getter for paypal config
     *
     * @return Mage_Paypal_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('paypal/config');
    }

    /**
     * Disable block output if banner turned off or PublisherId is miss
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_getConfig()->isMethodAvailable(Mage_Paypal_Model_Config::METHOD_BML)
            && !$this->_getConfig()->isMethodAvailable(Mage_Paypal_Model_Config::METHOD_WPP_PE_BML)) {
            return '';
        }
        $publisherId = $this->_getConfig()->getBmlPublisherId();
        $display = $this->_getConfig()->getBmlDisplay($this->_section);
        $position = $this->_getConfig()->getBmlPosition($this->_section);
        if (!$publisherId || $display == 0 || $this->_position != $position) {
            return '';
        }
        $this->setPublisherId($publisherId);
        $this->setSize($this->_getConfig()->getBmlSize($this->_section));
        return parent::_toHtml();
    }
}
