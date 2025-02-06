<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Backend ajax controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Ajax action for inline translation
     *
     * @SuppressWarnings("PHPMD.ExitExpression")
     */
    public function translateAction()
    {
        $translation = $this->getRequest()->getPost('translate');
        $area = $this->getRequest()->getPost('area');

        //filtering
        /** @var Mage_Core_Model_Input_Filter_MaliciousCode $filter */
        $filter = Mage::getModel('core/input_filter_maliciousCode');
        foreach ($translation as &$item) {
            $item['custom'] = $filter->filter($item['custom']);
        }

        echo Mage::helper('core/translate')->apply($translation, $area);
        exit();
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
