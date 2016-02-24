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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Helper_Sales extends Mage_Core_Helper_Abstract
{
    /**
     * Display price attribute value in base order currency and in place order currency
     *
     * @param   Varien_Object $dataObject
     * @param   string $code
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPriceAttribute($dataObject, $code, $strong = false, $separator = '<br/>')
    {
        return $this->displayPrices(
            $dataObject,
            $dataObject->getData('base_'.$code),
            $dataObject->getData($code),
            $strong,
            $separator
        );
    }

    /**
     * Get "double" prices html (block with base and place currency)
     *
     * @param   Varien_Object $dataObject
     * @param   float $basePrice
     * @param   float $price
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
        $order = false;
        if ($dataObject instanceof Mage_Sales_Model_Order) {
            $order = $dataObject;
        } else {
            $order = $dataObject->getOrder();
        }

        if ($order && $order->isCurrencyDifferent()) {
            $res = '<strong>';
            $res.= $order->formatBasePrice($basePrice);
            $res.= '</strong>'.$separator;
            $res.= '['.$order->formatPrice($price).']';
        } elseif ($order) {
            $res = $order->formatPrice($price);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        } else {
            $res = Mage::app()->getStore()->formatPrice($price);
            if ($strong) {
                $res = '<strong>'.$res.'</strong>';
            }
        }
        return $res;
    }

    /**
     * Filter collection by removing not available product types
     *
     * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function applySalableProductTypesFilter($collection)
    {
        $productTypes = Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray();
        $productTypes = array_keys($productTypes);
        foreach($collection->getItems() as $key => $item) {
            if ($item instanceof Mage_Catalog_Model_Product) {
                $type = $item->getTypeId();
            } else if ($item instanceof Mage_Sales_Model_Order_Item) {
                $type = $item->getProductType();
            } else if ($item instanceof Mage_Sales_Model_Quote_Item) {
                $type = $item->getProductType();
            } else {
                $type = '';
            }
            if (!in_array($type, $productTypes)) {
                $collection->removeItemByKey($key);
            }
        }
        return $collection;
    }

    /**
     * Escape string preserving links
     *
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */
    public function escapeHtmlWithLinks($data, $allowedTags = null)
    {
        if (!empty($data) && is_array($allowedTags) && in_array('a', $allowedTags)) {
            $links = array();
            $i = 1;
            $data = str_replace('%', '%%', $data);
            $regexp = "/<a\s[^>]*href\s*?=\s*?([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU";
            while (preg_match($regexp, $data, $matches)) {
                //Revert the sprintf escaping
                $url = str_replace('%%', '%', $matches[2]);
                $text = str_replace('%%', '%', $matches[3]);
                //Check for an valid url
                if ($url) {
                    $urlScheme = strtolower(parse_url($url, PHP_URL_SCHEME));
                    if ($urlScheme !== 'http' && $urlScheme !== 'https') {
                        $url = null;
                    }
                }
                //Use hash tag as fallback
                if (!$url) {
                    $url = '#';
                }
                //Recreate a minimalistic secure a tag
                $links[] = sprintf(
                    '<a href="%s">%s</a>',
                    htmlspecialchars($url, ENT_QUOTES, 'UTF-8', false),
                    parent::escapeHtml($text)
                );
                $data = str_replace($matches[0], '%' . $i . '$s', $data);
                ++$i;
            }
            $data = parent::escapeHtml($data, $allowedTags);
            return vsprintf($data, $links);
        }
        return parent::escapeHtml($data, $allowedTags);
    }
}
