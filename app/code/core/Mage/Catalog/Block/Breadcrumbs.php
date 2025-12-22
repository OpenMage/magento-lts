<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog breadcrumbs
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Block_Breadcrumbs extends Mage_Core_Block_Template
{
    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param  mixed  $store
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = (string) Mage::getStoreConfig('catalog/seo/title_separator', $store);
        return ' ' . $separator . ' ';
    }

    /**
     * Preparing layout
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        /** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbsBlock */
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb('home', [
                'label' => Mage::helper('catalog')->__('Home'),
                'title' => Mage::helper('catalog')->__('Go to Home Page'),
                'link' => Mage::getBaseUrl(),
            ]);

            $title = [];
            $path  = Mage::helper('catalog')->getBreadcrumbPath();

            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }

            if ($headBlock = $this->getLayout()->getBlock('head')) {
                $headBlock->setTitle(implode($this->getTitleSeparator(), array_reverse($title)));
            }
        }

        return parent::_prepareLayout();
    }
}
