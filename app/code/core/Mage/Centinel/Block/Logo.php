<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Centinel
 */

/**
 * Centinel payment form logo block
 *
 * @package    Mage_Centinel
 */
class Mage_Centinel_Block_Logo extends Mage_Core_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('centinel/logo.phtml');
    }

    /**
     * Return code of payment method
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getMethod()->getCode();
    }
}
