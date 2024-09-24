<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

/**
 * Catalog category attribute sets controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Catalog_Category_SetController extends Mage_Eav_Adminhtml_Set_AbstractController
{
    /**
     * Additional initialization
     *
     */
    protected function _construct(): void
    {
        $this->_entityCode = Mage_Catalog_Model_Category::ENTITY;
    }

    protected function _initAction()
    {
        parent::_initAction();

        $this->_title($this->__('Catalog'))
             ->_title($this->__('Attributes'))
             ->_title($this->__('Manage Category Attribute Sets'));

        $this->_setActiveMenu('catalog/attributes')
             ->_addBreadcrumb(
                 $this->__('Catalog'),
                 $this->__('Catalog')
             )
             ->_addBreadcrumb(
                 $this->__('Manage Category Attribute Sets'),
                 $this->__('Manage Category Attribute Sets')
             );

        return $this;
    }

    protected function _isAllowed(): bool
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/category_sets');
    }
}
