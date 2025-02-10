<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Payment
 */
class Mage_Payment_Block_Info_Purchaseorder extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/purchaseorder.phtml');
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/purchaseorder.phtml');
        return $this->toHtml();
    }
}
