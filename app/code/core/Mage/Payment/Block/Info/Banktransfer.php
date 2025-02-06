<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Payment
 */

/**
 * Block for Bank Transfer payment generic info
 *
 * @category   Mage
 * @package    Mage_Payment
 */
class Mage_Payment_Block_Info_Banktransfer extends Mage_Payment_Block_Info
{
    /**
     * Instructions text
     *
     * @var string|null
     */
    protected $_instructions;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/banktransfer.phtml');
    }

    /**
     * Get instructions text from order payment
     * (or from config, if instructions are missed in payment)
     *
     * @return string
     */
    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getInfo()->getAdditionalInformation('instructions');
            if (empty($this->_instructions)) {
                $this->_instructions = $this->getMethod()->getInstructions();
            }
        }
        return $this->_instructions;
    }
}
