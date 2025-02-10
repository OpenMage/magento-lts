<?php
/**
 * Centinel payment form logo block
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Centinel
 */
class Mage_Centinel_Block_Logo extends Mage_Core_Block_Template
{
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
