<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml Google Base Items Grids Container
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Block_Adminhtml_Items extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('googlebase/items.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('item', $this->getLayout()->createBlock('googlebase/adminhtml_items_item'));
        $this->setChild('product', $this->getLayout()->createBlock('googlebase/adminhtml_items_product'));
        $this->setChild('store_switcher', $this->getLayout()->createBlock('googlebase/adminhtml_store_switcher'));
    }

    public function getAddButtonHtml()
    {
        $addButtonData = array(
            'id'    => 'products_grid_button',
            'label' => $this->__('View Available Products'),
        );
        return $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setData($addButtonData)
            ->toHtml();
    }

    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    public function getCaptchaHtml()
    {
        return $this->getLayout()->createBlock('googlebase/adminhtml_captcha')
            ->setGbaseCaptchaToken($this->getGbaseCaptchaToken())
            ->setGbaseCaptchaUrl($this->getGbaseCaptchaUrl())
            ->toHtml();
    }

    public function getStore()
    {
        return $this->_getData('store');
    }
}
