<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Helper_Catalog_Product_Configuration extends Mage_Core_Helper_Abstract implements Mage_Catalog_Helper_Product_Configuration_Interface
{
    protected $_moduleName = 'Mage_Downloadable';

    /**
     * Retrieves item links options
     *
     * @return array
     */
    public function getLinks(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $itemLinks = [];
        $linkIds = $item->getOptionByCode('downloadable_link_ids');
        if ($linkIds) {
            /** @var Mage_Downloadable_Model_Product_Type $productType */
            $productType = $product->getTypeInstance(true);
            $productLinks = $productType->getLinks($product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($productLinks[$linkId])) {
                    $itemLinks[] = $productLinks[$linkId];
                }
            }
        }

        return $itemLinks;
    }

    /**
     * Retrieves product links section title
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getLinksTitle($product)
    {
        $title = $product->getLinksTitle();
        if (strlen($title)) {
            return $title;
        }

        return (string) Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Retrieves product options
     *
     * @return array
     */
    public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = Mage::helper('catalog/product_configuration')->getOptions($item);

        $links = $this->getLinks($item);
        if ($links) {
            $linksOption = [
                'label' => $this->getLinksTitle($item->getProduct()),
                'value' => [],
            ];
            foreach ($links as $link) {
                $linksOption['value'][] = $link->getTitle();
            }

            $options[] = $linksOption;
        }

        return $options;
    }
}
