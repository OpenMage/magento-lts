<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Rss_OrderController extends Mage_Adminhtml_Controller_Rss_Abstract
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    const ADMIN_RESOURCE = 'sales/order/actions/view';

    public function newAction()
    {
        if ($this->checkFeedEnable('order/new')) {
            $this->loadLayout(false);
            $this->renderLayout();
        }
    }
}
