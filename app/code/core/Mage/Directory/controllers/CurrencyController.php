<?php
/**
 * Currency controller
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Directory
 */
class Mage_Directory_CurrencyController extends Mage_Core_Controller_Front_Action
{
    public function switchAction()
    {
        if ($curency = (string) $this->getRequest()->getParam('currency')) {
            Mage::app()->getStore()->setCurrentCurrencyCode($curency);
        }
        $this->_redirectReferer(Mage::getBaseUrl());
    }
}
