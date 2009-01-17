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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Catalog breadcrumbs
 *
 * @package     Mage
 * @subpackage  Mage_Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home',
                array('label'=>Mage::helper('catalog')->__('Home'), 'title'=>Mage::helper('catalog')->__('Go to Home Page'), 'link'=>Mage::getBaseUrl())
            );

            $title = (string)Mage::getStoreConfig('system/store/name');
            $path = Mage::helper('catalog')->getBreadcrumbPath($this->getCategory());
            foreach ($path as $name=>$breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title = $breadcrumb['label'].' '.Mage::getStoreConfig('catalog/seo/title_separator').' '.$title;
            }

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle($title);
            }
        }
        return parent::_prepareLayout();
    }

}
