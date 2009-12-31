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
 * @category    Mage
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce admin left menu
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Block_Adminhtml_Import_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('oscommerce_import_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('oscommerce')->__('osCommerce Profile'));
    }

    protected function _beforeToHtml()
    {
        $model = Mage::registry('oscommerce_adminhtml_import');

        $generalBlock = $this->getLayout()->createBlock('oscommerce/adminhtml_import_edit_tab_general');
        $generalBlock->addData($model->getData());

        $new = !$model->getId();

        $this->addTab('general', array(
            'label'     => Mage::helper('oscommerce')->__('General Information'),
            'content'   => $generalBlock->initForm()->toHtml(),
            'active'    => true,
        ));

        if (!$new) {
            $this->addTab('run', array(
                'label'     => Mage::helper('oscommerce')->__('Run Profile'),
                'content'   => $this->getLayout()->createBlock('oscommerce/adminhtml_import_edit_tab_run')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }
}
