<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

/**
 * Downloadable Product Links part block
 *
 * @package    Mage_Downloadable
 */
class Mage_Downloadable_Block_Catalog_Product_Links extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * @return bool
     */
    public function getLinksPurchasedSeparately()
    {
        return $this->getProduct()->getLinksPurchasedSeparately();
    }

    /**
     * @return bool
     */
    public function getLinkSelectionRequired()
    {
        /** @var Mage_Downloadable_Model_Product_Type $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        return $productType->getLinkSelectionRequired($this->getProduct());
    }

    /**
     * @return bool
     */
    public function hasLinks()
    {
        /** @var Mage_Downloadable_Model_Product_Type $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        return $productType->hasLinks($this->getProduct());
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        /** @var Mage_Downloadable_Model_Product_Type $productType */
        $productType = $this->getProduct()->getTypeInstance(true);
        return $productType->getLinks($this->getProduct());
    }

    /**
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getFormattedLinkPrice($link)
    {
        $price = $link->getPrice();
        $store = $this->getProduct()->getStore();

        if ($price == 0) {
            return '';
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $taxHelper = Mage::helper('tax');
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = $this->helper('core');
        $_priceInclTax = $taxHelper->getPrice($link->getProduct(), $price, true);
        $_priceExclTax = $taxHelper->getPrice($link->getProduct(), $price);

        $priceStr = '<span class="price-notice">+';
        if ($taxHelper->displayPriceIncludingTax()) {
            $priceStr .= $coreHelper::currencyByStore($_priceInclTax, $store);
        } elseif ($taxHelper->displayPriceExcludingTax()) {
            $priceStr .= $coreHelper::currencyByStore($_priceExclTax, $store);
        } elseif ($taxHelper->displayBothPrices()) {
            $priceStr .= $coreHelper::currencyByStore($_priceExclTax, $store);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' (+' . $coreHelper::currencyByStore($_priceInclTax, $store) . ' ' . $this->__('Incl. Tax') . ')';
            }
        }

        return $priceStr . '</span>';
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = $this->helper('core');
        $store = $this->getProduct()->getStore();
        return $helper::currencyByStore($price, $store, false);
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $config = [];
        $coreHelper = Mage::helper('core');

        foreach ($this->getLinks() as $link) {
            $config[$link->getId()] = $coreHelper::currency($link->getPrice(), false, false);
        }

        return $coreHelper->jsonEncode($config);
    }

    /**
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     */
    public function getLinkSamlpeUrl($link)
    {
        return $this->getUrl('downloadable/download/linkSample', ['link_id' => $link->getId()]);
    }

    /**
     * Return title of links section
     *
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->getProduct()->getLinksTitle()) {
            return $this->getProduct()->getLinksTitle();
        }
        return Mage::getStoreConfig(Mage_Downloadable_Model_Link::XML_PATH_LINKS_TITLE);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

    /**
     * Returns whether link checked by default or not
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return bool
     */
    public function getIsLinkChecked($link)
    {
        $configValue = $this->getProduct()->getPreconfiguredValues()->getLinks();
        if (!$configValue || !is_array($configValue)) {
            return false;
        }

        return in_array($link->getId(), $configValue);
    }

    /**
     * Returns value for link's input checkbox - either 'checked' or ''
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     */
    public function getLinkCheckedValue($link)
    {
        return $this->getIsLinkChecked($link) ? 'checked' : '';
    }
}
