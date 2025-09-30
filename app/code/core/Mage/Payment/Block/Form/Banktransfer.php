<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * Block for Bank Transfer payment method form
 *
 * @package    Mage_Payment
 */
class Mage_Payment_Block_Form_Banktransfer extends Mage_Payment_Block_Form
{
    /**
     * Instructions text
     *
     * @var string|null
     */
    protected $_instructions;

    /**
     * Block construction. Set block template.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/form/banktransfer.phtml');
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getInstructions();
        }
        return $this->_instructions;
    }
}
