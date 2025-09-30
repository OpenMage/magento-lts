<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rss
 */

/**
 * Review form block
 *
 * @package    Mage_Rss
 */
class Mage_Rss_Block_Catalog_Salesrule extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_salesrule_' . $this->getStoreId() . '_' . $this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    /**
     * Generate RSS XML with sales rules data
     *
     * @return string
     */
    protected function _toHtml()
    {
        $storeId       = $this->_getStoreId();
        $websiteId     = Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroup = $this->_getCustomerGroupId();
        $now           = date('Y-m-d');
        $url           = Mage::getUrl('');
        $newUrl        = Mage::getUrl('rss/catalog/salesrule');
        $lang          = Mage::getStoreConfig('general/locale/code');
        $title         = Mage::helper('rss')->__('%s - Discounts and Coupons', Mage::app()->getStore($storeId)->getName());

        /** @var Mage_Rss_Model_Rss $rssObject */
        $rssObject = Mage::getModel('rss/rss');
        /** @var Mage_SalesRule_Model_Resource_Rule_Collection $collection */
        $collection = Mage::getModel('salesrule/rule')->getResourceCollection();

        $data = [
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
            'language'    => $lang,
        ];
        $rssObject->_addHeader($data);

        $collection->addWebsiteGroupDateFilter($websiteId, $customerGroup, $now)
            ->addFieldToFilter('is_rss', 1)
            ->setOrder('from_date', 'desc');
        $collection->load();

        foreach ($collection as $sr) {
            $description = '<table><tr>' .
            '<td style="text-decoration:none;">' . $sr->getDescription() .
            '<br/>Discount Start Date: ' . $this->formatDate($sr->getFromDate(), 'medium') .
            ($sr->getToDate() ? ('<br/>Discount End Date: ' . $this->formatDate($sr->getToDate(), 'medium')) : '') .
            ($sr->getCouponCode() ? '<br/> Coupon Code: ' . $this->escapeHtml($sr->getCouponCode()) . '' : '') .
            '</td>' .
            '</tr></table>';
            $data = [
                'title'       => $sr->getName(),
                'description' => $description,
                'link'        => $url,
            ];
            $rssObject->_addEntry($data);
        }

        return $rssObject->createRssXml();
    }
}
