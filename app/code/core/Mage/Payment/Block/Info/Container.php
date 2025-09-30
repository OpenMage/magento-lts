<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Payment information container block
 *
 * @package    Mage_Payment
 */
class Mage_Payment_Block_Info_Container extends Mage_Core_Block_Template
{
    /**
     * Add payment info block to layout
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        if ($info = $this->getPaymentInfo()) {
            $this->setChild(
                $this->_getInfoBlockName(),
                Mage::helper('payment')->getInfoBlock($info),
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve info block name
     *
     * @return false|string
     */
    protected function _getInfoBlockName()
    {
        if ($info = $this->getPaymentInfo()) {
            return 'payment.info.' . $info->getMethodInstance()->getCode();
        }
        return false;
    }

    /**
     * Retrieve payment info model
     *
     * @return Mage_Payment_Model_Info|false
     */
    public function getPaymentInfo()
    {
        return false;
    }

    /**
     * Declare info block template
     *
     * @param   string $method
     * @param   string $template
     * @return  Mage_Payment_Block_Info_Container
     */
    public function setInfoTemplate($method = '', $template = '')
    {
        if ($info = $this->getPaymentInfo()) {
            if ($info->getMethodInstance()->getCode() == $method) {
                $this->getChild($this->_getInfoBlockName())->setTemplate($template);
            }
        }
        return $this;
    }
}
