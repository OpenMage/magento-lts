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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'Mage/Eav/controllers/Adminhtml/Attribute/AbstractController.php';

/**
 * Catalog category attribute controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Catalog_Category_AttributeController extends Mage_Eav_Adminhtml_Attribute_AbstractController
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->_entityCode = Mage_Catalog_Model_Category::ENTITY;
    }

    protected function _initAction()
    {
        parent::_initAction();

        $this->_title($this->__('Catalog'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Category Attributes'));

        $this->_setActiveMenu('catalog/attributes')
             ->_addBreadcrumb(
                 $this->__('Catalog'),
                 $this->__('Catalog')
             )
             ->_addBreadcrumb(
                 $this->__('Manage Category Attributes'),
                 $this->__('Manage Category Attributes')
             );

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/category_attributes');
    }

}
