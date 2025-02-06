<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Product tax admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_TaxController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Set tax ignore notification flag and redirect back
     */
    public function ignoreTaxNotificationAction()
    {
        $section = $this->getRequest()->getParam('section');
        if ($section) {
            Mage::helper('tax')->setIsIgnored('tax/ignore_notification/' . $section, true);
        }
        $this->_redirectReferer();
    }

    /**
     * Check is allowed access to action
     *
     * @return true
     */
    protected function _isAllowed()
    {
        return true;
    }
}
