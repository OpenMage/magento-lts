<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Catalog
 */

/**
 * Site Map category block
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
abstract class Mage_Catalog_Block_Seo_Sitemap_Abstract extends Mage_Core_Block_Template
{
    /**
     * Init pager
     *
     * @param string $pagerName
     */
    public function bindPager($pagerName)
    {
        $pager = $this->getLayout()->getBlock($pagerName);
        /** @var Mage_Page_Block_Html_Pager $pager */
        if ($pager) {
            $pager->setAvailableLimit([50 => 50]);
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(false);
        }
    }

    /**
     * Get item URL
     *
     * In most cases should be overridden in descendant blocks
     *
     * @param Mage_Catalog_Block_Seo_Sitemap_Abstract $item
     * @return string
     */
    public function getItemUrl($item)
    {
        return $item->getUrl();
    }
}
