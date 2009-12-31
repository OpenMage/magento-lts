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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable Product Links part block
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Catalog_Product_Links extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getLinksPurchasedSeparately()
    {
        return $this->getProduct()->getLinksPurchasedSeparately();
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getLinkSelectionRequired()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->getLinkSelectionRequired($this->getProduct());
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function hasLinks()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->hasLinks($this->getProduct());
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->getLinks($this->getProduct());
    }

    /**
     * Enter description here...
     *
     * @param Mage_Downloadable_Model_Link $link
     * @return string
     */
    public function getFormattedLinkPrice($link)
    {
        $price = $link->getPrice();

        if (0 == $price) {
            return '';
        }

        $_priceInclTax = Mage::helper('tax')->getPrice($link->getProduct(), $price, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($link->getProduct(), $price);

        $priceStr = '<span class="price-notice">+';
        if (Mage::helper('tax')->displayPriceIncludingTax()) {
            $priceStr .= $this->helper('core')->currency($_priceInclTax, true, true);
        } elseif (Mage::helper('tax')->displayPriceExcludingTax()) {
            $priceStr .= $this->helper('core')->currency($_priceExclTax, true, true);
        } elseif (Mage::helper('tax')->displayBothPrices()) {
            $priceStr .= $this->helper('core')->currency($_priceExclTax, true, true);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' (+'.$this->helper('core')
                    ->currency($_priceInclTax, true, true).' '.$this->__('Incl. Tax').')';
            }
        }
        $priceStr .= '</span>';

        return $priceStr;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();

        foreach ($this->getLinks() as $link) {
            $config[$link->getId()] = Mage::helper('core')->currency($link->getPrice(), false, false);
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    public function getLinkSamlpeUrl($link)
    {
        return $this->getUrl('downloadable/download/linkSample', array('link_id' => $link->getId()));
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

}
