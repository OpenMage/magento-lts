<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Payment
 */

/**
 * @package    Mage_Payment
 */
class Mage_Payment_Block_Form_Checkmo extends Mage_Payment_Block_Form
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/form/checkmo.phtml');
    }
}
