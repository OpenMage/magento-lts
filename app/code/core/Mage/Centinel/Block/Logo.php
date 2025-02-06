<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Centinel
 */

/**
 * Centinel payment form logo block
 *
 * @category   Mage
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
