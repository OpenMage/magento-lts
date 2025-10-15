<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Helper_Sales extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Adminhtml';

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
            $dataObject->getData('base_' . $code),
            $dataObject->getData($code),
            $strong,
            $separator,
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
            $res .= $order->formatBasePrice($basePrice);
            $res .= '</strong>' . $separator;
            $res .= '[' . $order->formatPrice($price) . ']';
        } elseif ($order) {
            $res = $order->formatPrice($price);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        } else {
            $res = Mage::app()->getStore()->formatPrice($price);
            if ($strong) {
                $res = '<strong>' . $res . '</strong>';
            }
        }

        return $res;
    }

    /**
     * Filter collection by removing not available product types
     *
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function applySalableProductTypesFilter($collection)
    {
        $productTypes = Mage::getConfig()->getNode('adminhtml/sales/order/create/available_product_types')->asArray();
        $productTypes = array_keys($productTypes);
        foreach ($collection->getItems() as $key => $item) {
            if ($item instanceof Mage_Catalog_Model_Product) {
                $type = $item->getTypeId();
            } elseif ($item instanceof Mage_Sales_Model_Order_Item) {
                $type = $item->getProductType();
            } elseif ($item instanceof Mage_Sales_Model_Quote_Item) {
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
     * @param string|string[] $data
     * @param array|null $allowedTags
     * @return null|string|string[]
     */
    public function escapeHtmlWithLinks($data, $allowedTags = null)
    {
        if (!empty($data) && is_array($allowedTags) && in_array('a', $allowedTags)) {
            $links = [];
            $i = 1;
            $data = str_replace('%', '%%', $data);
            $regexp = "/<a\s[^>]*href\s*?=\s*?([\"\']??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/siU";
            while (preg_match($regexp, $data, $matches)) {
                //Revert the sprintf escaping
                $url = str_replace('%%', '%', $matches[2]);
                $text = str_replace('%%', '%', $matches[3]);
                //Check for a valid url
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
                    parent::escapeHtml($text),
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
