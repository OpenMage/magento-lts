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
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml tag accordion
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tag_Edit_Accordion extends Mage_Adminhtml_Block_Widget_Accordion
{
    /**
     * Add products and customers accordion to layout
     *
     */
    protected function _prepareLayout()
    {
        if (is_null(Mage::registry('current_tag')->getId())) {
            return $this;
        }

        $tagModel = Mage::registry('current_tag');

        $this->setId('tag_customer_and_product_accordion');

        $this->addItem('tag_customer', array(
            'title'         => Mage::helper('tag')->__('Customers Submitted this Tag'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/customer', array('ret' => 'all', 'tag_id'=>$tagModel->getId(), 'store'=>$tagModel->getStoreId())),
        ));

        $this->addItem('tag_product', array(
            'title'         => Mage::helper('tag')->__('Products Tagged by Customers'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/product', array('ret' => 'all', 'tag_id'=>$tagModel->getId(), 'store'=>$tagModel->getStoreId())),
        ));
        return parent::_prepareLayout();
    }
}
