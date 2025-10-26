<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

/**
 * PayPal online logo with additional options
 *
 * @package    Mage_Paypal
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
            && !$this->_getConfig()->isMethodAvailable(Mage_Paypal_Model_Config::METHOD_WPP_PE_BML)
        ) {
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
