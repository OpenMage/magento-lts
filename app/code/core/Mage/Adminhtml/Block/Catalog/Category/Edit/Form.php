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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Edit_Form extends Mage_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Additional buttons on category page
     *
     * @var array
     */
    protected $_additionalButtons = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/category/edit/form.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('tabs',
            $this->getLayout()->createBlock('adminhtml/catalog_category_tabs', 'tabs')
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Save Category'),
                    'onclick'   => "categorySubmit('".$this->getSaveUrl()."',true)",
                    'class' => 'save'
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Delete Category'),
                    'onclick'   => "categoryDelete('".$this->getUrl('*/*/delete', array('_current'=>true))."',true)",
                    'class' => 'delete'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Reset'),
                    'onclick'   => "categoryReset('".$this->getUrl('*/*/edit', array('_current'=>true))."',true)"
                ))
        );
        return parent::_prepareLayout();
    }

    public function getStoreConfigurationUrl()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $params = array();
//        $params = array('section'=>'catalog');
        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $params['website'] = $store->getWebsite()->getCode();
            $params['store']   = $store->getCode();
        }
        return $this->getUrl('*/system_store', $params);
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getSaveButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml('save_button');
        }
        return '';
    }

    public function getResetButtonHtml()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getChildHtml('reset_button');
        }
        return '';
    }

    /**
     * Retrieve additional buttons html
     *
     * @return string
     */
    public function getAdditionalButtonsHtml()
    {
        $html = '';
        foreach ($this->_additionalButtons as $childName) {
            $html .= $this->getChildHtml($childName);
        }
        return $html;
    }

    /**
     * Add additional button
     *
     * @param string $alias
     * @param array $config
     * @return Mage_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    public function addAdditionalButton($alias, $config)
    {
        $this->setChild($alias . '_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')->addData($config));
        $this->_additionalButtons[$alias] = $alias . '_button';
        return $this;
    }

    /**
     * Remove additional button
     *
     * @param string $alias
     * @return Mage_Adminhtml_Block_Catalog_Category_Edit_Form
     */
    public function removeAdditionalButton($alias)
    {
        if (isset($this->_additionalButtons[$alias])) {
            $this->unsetChild($this->_additionalButtons[$alias]);
            unset($this->_additionalButtons[$alias]);
        }

        return $this;
    }

    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    public function getHeader()
    {
        if ($this->hasStoreRootCategory()) {
            return $this->getCategoryId() ? $this->getCategoryName() : Mage::helper('catalog')->__('New Category');
        }
        return Mage::helper('catalog')->__('Set Root Category For Store');
    }

    public function getDeleteUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/delete', $params);
    }

    public function getProductsJson()
    {
        $products = $this->getCategory()->getProductsPosition();
        if (!empty($products)) {
            return Zend_Json::encode($products);
        }
        return '{}';
    }

    public function isAjax()
    {
        return Mage::app()->getRequest()->isXmlHttpRequest() || Mage::app()->getRequest()->getParam('isAjax');
    }
}
