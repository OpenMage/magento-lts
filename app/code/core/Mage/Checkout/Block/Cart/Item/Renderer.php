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
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart item render block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart_Item_Renderer extends Mage_Core_Block_Template
{
    protected $_item;
    protected $_productUrl = null;
    protected $_productThumbnail = null;

    /**
     * Set item for render
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Checkout_Block_Cart_Item_Renderer
     */
    public function setItem(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get quote item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get item product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->getItem()->getProduct();
    }

    public function overrideProductThumbnail($productThumbnail)
    {
        $this->_productThumbnail = $productThumbnail;
        return $this;
    }

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        if (!is_null($this->_productThumbnail)) {
            return $this->_productThumbnail;
        }
        return $this->helper('catalog/image')->init($this->getProduct(), 'thumbnail');
    }

    public function overrideProductUrl($productUrl)
    {
        $this->_productUrl = $productUrl;
        return $this;
    }

    /**
     * Get url to item product
     *
     * @return string
     */
    public function getProductUrl()
    {
        if (!is_null($this->_productUrl)) {
            return $this->_productUrl;
        }
        return $this->getProduct()->getProductUrl();
    }

    /**
     * Get item product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * Get product customize options
     *
     * @return array || false
     */
    public function getProductOptions()
    {
        $options = array();
        if ($optionIds = $this->getItem()->getOptionByCode('option_ids')) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $this->getProduct()->getOptionById($optionId)) {
                    $formatedValue = '';
                    $optionGroup = $option->getGroupByType();
                    $optionValue = $this->getItem()->getOptionByCode('option_' . $option->getId())->getValue();
                    if ($option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
                        || $option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                        foreach(split(',', $optionValue) as $value) {
                            $formatedValue .= $option->getValueById($value)->getTitle() . ', ';
                        }
                        $formatedValue = Mage::helper('core/string')->substr($formatedValue, 0, -2);
                    } elseif ($optionGroup == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                        $formatedValue = $option->getValueById($optionValue)->getTitle();
                    } else {
                        $formatedValue = $optionValue;
                    }
                    $options[] = array(
                        'label' => $option->getTitle(),
                        'value' => $this->htmlEscape($formatedValue),
                    );
                }
            }
        }
        if ($addOptions = $this->getItem()->getOptionByCode('additional_options')) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }
        return $options;
    }

    /**
     * Get list of all otions for product
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->getProductOptions();
    }

    /**
     * Get item delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            'checkout/cart/delete',
            array(
                'id'=>$this->getItem()->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl()
            )
        );
    }

    /**
     * Get quote item qty
     *
     * @return mixed
     */
    public function getQty()
    {
        return $this->getItem()->getQty()*1;
    }

    /**
     * Check item is in stock
     *
     * @return bool
     */
    public function getIsInStock()
    {
        if ($this->getItem()->getProduct()->isSaleable()) {
            if ($this->getItem()->getProduct()->getQty()>=$this->getItem()->getQty()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve item messages
     * Return array with keys
     *
     * type     => type of a message
     * text     => the message text
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        if ($this->getItem()->getMessage(false)) {
            foreach ($this->getItem()->getMessage(false) as $message) {
                $messages[] = array(
                    'text'  => $message,
                    'type'  => $this->getItem()->getHasError() ? 'error' : 'notice'
                );
            }
        }
        return array_unique($messages);
    }

    public function getFormatedOptionValue($optionValue)
    {
        $formateOptionValue = array();
        if (is_array($optionValue)) {
            $_truncatedValue = implode("\n", $optionValue);
            $_truncatedValue = nl2br($_truncatedValue);
            return array('value' => $_truncatedValue);
        } else {
            $_truncatedValue = Mage::helper('core/string')->truncate($optionValue, 55, '');
            $_truncatedValue = nl2br($_truncatedValue);
        }

        $formateOptionValue = array(
            'value' => $_truncatedValue
        );

        if (Mage::helper('core/string')->strlen($optionValue) > 55) {
            $formateOptionValue['value'] = $formateOptionValue['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $formateOptionValue = array_merge($formateOptionValue, array('full_view' => $optionValue));

        }

        return $formateOptionValue;
    }
}