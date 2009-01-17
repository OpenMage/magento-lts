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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cache management edit page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Cache_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/cache/edit.phtml');
        $this->setTitle('Cache Management');
    }

    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Save cache settings'),
                    'onclick'   => 'configForm.submit()',
                    'class' => 'save',
                ))
        );
        return parent::_prepareLayout();
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    public function initForm()
    {
        $this->setChild('form',
            $this->getLayout()->createBlock('adminhtml/system_cache_form')
                ->initForm()
        );
        return $this;
    }

    public function getCatalogData()
    {
        return array(
            'refresh_catalog_rewrites'   => array(
                'name'      => 'refresh_catalog_rewrites',
                'label'     => Mage::helper('adminhtml')->__('Catalog Rewrites'),
                'action'    => Mage::helper('adminhtml')->__('Refresh'),
            ),
            'clear_images_cache'         => array(
                'name'      => 'clear_images_cache',
                'label'     => Mage::helper('adminhtml')->__('Images Cache'),
                'action'    => Mage::helper('adminhtml')->__('Clear'),
            ),
            'refresh_layered_navigation' => array(
                'name'      => 'refresh_layered_navigation',
                'label'     => Mage::helper('adminhtml')->__('Layered Navigation Indices'),
                'action'    => Mage::helper('adminhtml')->__('Refresh'),
            )
        );
    }
}