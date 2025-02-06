<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Rss_OrderController extends Mage_Adminhtml_Controller_Rss_Abstract
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/order/actions/view';

    public function newAction()
    {
        if ($this->checkFeedEnable('order/new')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
}
