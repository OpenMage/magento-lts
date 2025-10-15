<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Weee
 */

/**
 * @package    Mage_Weee
 */
class Mage_Weee_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config Path for FPT
     */
    public const XML_PATH_FPT_ENABLED = 'tax/weee/enable';

    /**
     *'FPT Tax Configuration' for TAXED
     */
    public const TAXED = '1';

    /**
     *'FPT Tax Configuration' for LOADED_AND_DISPLAY_WITH_TAX
     */
    public const LOADED_AND_DISPLAY_WITH_TAX = '2';

    protected $_moduleName = 'Mage_Weee';

    /**
     * Current store, in the case of backend order, it could be different from admin store
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * @var array
     */
    protected $_storeDisplayConfig   = [];

    /**
     * Get weee amount display type on product view page
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  int
     */
    public function getPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display', $store);
    }

    /**
     * Get weee amount display type on product list page
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  int
     */
    public function getListPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_list', $store);
    }

    /**
     * Get weee amount display type in sales modules
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  int
     */
    public function getSalesPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_sales', $store);
    }

    /**
     * Get weee amount display type in email templates
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  int
     */
    public function getEmailPriceDisplayType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/display_email', $store);
    }

    /**
     * Check if weee tax amount should be discounted
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  bool
     */
    public function isDiscounted($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/discount', $store);
    }

    /**
     * Check if weee tax amount should be taxable
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  bool
     */
    public function isTaxable($store = null)
    {
        return Mage::getStoreConfig('tax/weee/apply_vat', $store) == self::TAXED ||
            Mage::getStoreConfig('tax/weee/apply_vat', $store) == self::LOADED_AND_DISPLAY_WITH_TAX;
    }

    /**
     * Returns true if default store tax is already applied to the FPT(weee)
     *
     * @param bool|int|Mage_Core_Model_Store|null|string $store
     * @return bool
     */
    public function isTaxIncluded($store = null)
    {
        return Mage::getStoreConfig('tax/weee/apply_vat', $store) == self::LOADED_AND_DISPLAY_WITH_TAX;
    }

    /**
     * Get Weee Tax Configuration Type
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  int
     */
    public function getTaxType($store = null)
    {
        return Mage::getStoreConfig('tax/weee/apply_vat', $store);
    }

    /**
     * Check if weee tax amount should be included to subtotal
     *
     * @param   bool|int|Mage_Core_Model_Store|null|string $store
     * @return  bool
     */
    public function includeInSubtotal($store = null)
    {
        return Mage::getStoreConfigFlag('tax/weee/include_in_subtotal', $store);
    }

    /**
     * Get weee tax amount for product based on shipping and billing addresses, website and tax settings
     *
     * @param   Mage_Catalog_Model_Product $product
     * @param   null|Mage_Customer_Model_Address_Abstract $shipping
     * @param   null|Mage_Customer_Model_Address_Abstract $billing
     * @param   mixed $website
     * @param   bool $calculateTaxes
     * @return  float
     */
    public function getAmount($product, $shipping = null, $billing = null, $website = null, $calculateTaxes = false)
    {
        if ($this->isEnabled()) {
            return Mage::getSingleton('weee/tax')->
                getWeeeAmount($product, $shipping, $billing, $website, $calculateTaxes);
        }

        return 0;
    }

    /**
     * Returns display type for price accordingly to current zone
     *
     * @param mixed                      $product
     * @param array|int|null             $compareTo
     * @param string                     $zone
     * @param Mage_Core_Model_Store      $store
     * @return bool|int
     */
    public function typeOfDisplay($product, $compareTo = null, $zone = null, $store = null)
    {
        if (!$this->isEnabled($store)) {
            return false;
        }

        switch ($zone) {
            case 'product_view':
                $type = $this->getPriceDisplayType($store);
                break;
            case 'product_list':
                $type = $this->getListPriceDisplayType($store);
                break;
            case 'sales':
                $type = $this->getSalesPriceDisplayType($store);
                break;
            case 'email':
                $type = $this->getEmailPriceDisplayType($store);
                break;
            default:
                if (Mage::registry('current_product')) {
                    $type = $this->getPriceDisplayType($store);
                } else {
                    $type = $this->getListPriceDisplayType($store);
                }

                break;
        }

        if (is_null($compareTo)) {
            return $type;
        } elseif (is_array($compareTo)) {
            return in_array($type, $compareTo);
        } else {
            return $type == $compareTo;
        }
    }

    /**
     * Proxy for Mage_Weee_Model_Tax::getProductWeeeAttributes()
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|false|Varien_Object   $shipping
     * @param null|false|Varien_Object   $billing
     * @param int|Mage_Core_Model_Website|null|string|true $website
     * @param bool                       $calculateTaxes
     * @return array
     */
    public function getProductWeeeAttributes(
        $product,
        $shipping = null,
        $billing = null,
        $website = null,
        $calculateTaxes = false
    ) {
        return Mage::getSingleton('weee/tax')
            ->getProductWeeeAttributes($product, $shipping, $billing, $website, $calculateTaxes);
    }

    /**
     * Returns applied weee taxes
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract|Varien_Object $item
     * @return array
     */
    public function getApplied($item)
    {
        if ($item instanceof Mage_Sales_Model_Quote_Item_Abstract) {
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                $result = [];
                foreach ($item->getChildren() as $child) {
                    $childData = $this->getApplied($child);
                    if (is_array($childData)) {
                        $result = array_merge($result, $childData);
                    }
                }

                return $result;
            }
        }

        /**
         * if order item data is old enough then weee_tax_applied cab be
         * not valid serialized data
         */
        $data = $item->getWeeeTaxApplied();
        if (empty($data)) {
            return [];
        }

        return unserialize($item->getWeeeTaxApplied(), ['allowed_classes' => false]);
    }

    /**
     * Sets applied weee taxes
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @param array                                $value
     * @return $this
     */
    public function setApplied($item, $value)
    {
        $item->setWeeeTaxApplied(serialize($value));
        return $this;
    }

    /**
     * Returns array of weee attributes allowed for display
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductWeeeAttributesForDisplay($product)
    {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes($product, null, null, null, $this->typeOfDisplay($product, 1));
        }

        return [];
    }

    /**
     * Get Product Weee attributes for price renderer
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|false|Varien_Object $shipping Shipping Address
     * @param null|false|Varien_Object $billing Billing Address
     * @param int|Mage_Core_Model_Website|null|string|true $website
     * @param mixed $calculateTaxes
     * @return array
     */
    public function getProductWeeeAttributesForRenderer(
        $product,
        $shipping = null,
        $billing = null,
        $website = null,
        $calculateTaxes = false
    ) {
        if ($this->isEnabled()) {
            return $this->getProductWeeeAttributes(
                $product,
                $shipping,
                $billing,
                $website,
                $calculateTaxes ? $calculateTaxes : $this->typeOfDisplay($product, 1),
            );
        }

        return [];
    }

    /**
     * Returns amount to display excluding taxes
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getAmountForDisplay($product)
    {
        if ($this->isEnabled()) {
            $attributes = $this->getProductWeeeAttributesForRenderer(
                $product,
                null,
                null,
                null,
                true,
            );

            if (is_array($attributes)) {
                $amount = 0;
                foreach ($attributes as $attribute) {
                    /** @var Varien_Object $attribute */
                    $amount += $attribute->getAmount();
                }

                return $amount;
            }
        }

        return 0;
    }

    /**
     * Returns amount to display including taxes
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getAmountForDisplayInclTaxes($product)
    {
        if ($this->isEnabled()) {
            $attributes = $this->getProductWeeeAttributesForRenderer(
                $product,
                null,
                null,
                null,
                true,
            );
            return $this->getAmountInclTaxes($attributes);
        }

        return 0;
    }

    /**
     * Returns original amount
     *
     * @param Mage_Catalog_Model_Product $product
     * @return float|int
     */
    public function getOriginalAmount($product)
    {
        if ($this->isEnabled()) {
            return Mage::getModel('weee/tax')->getWeeeAmount($product, null, null, null, false, true);
        }

        return 0;
    }

    /**
     * Adds HTML containers and formats tier prices accordingly to the currency used
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array                      $tierPrices
     * @param bool                    $includeIndex
     * @return $this
     */
    public function processTierPrices($product, &$tierPrices, $includeIndex = true)
    {
        $weeeAmountInclTax = $this->getAmountForDisplayInclTaxes($product);
        $weeeAmount = $this->getAmountForDisplay($product);
        $store = Mage::app()->getStore();
        foreach ($tierPrices as $index => &$tier) {
            $spanTag = '<span class="price tier-' . ($includeIndex ? $index : 'fixed');
            $html = $store->formatPrice($store->convertPrice(
                Mage::helper('tax')->getPrice($product, $tier['website_price'], true) + $weeeAmountInclTax,
            ), false);
            $tier['formated_price_incl_weee'] = $spanTag . '-incl-tax">' . $html . '</span>';
            $html = $store->formatPrice($store->convertPrice(
                Mage::helper('tax')->getPrice($product, $tier['website_price']) + $weeeAmount,
            ), false);
            $tier['formated_price_incl_weee_only'] = $spanTag . '">' . $html . '</span>';
            $tier['formated_weee'] = $store->formatPrice($store->convertPrice($weeeAmount));
        }

        return $this;
    }

    /**
     * Check if fixed taxes are used in system
     *
     * @param bool|int|Mage_Core_Model_Store|null|string $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        if ($store == null && $this->_store) {
            //This is needed when order is created from backend
            $store = $this->_store;
        }

        return Mage::getStoreConfig(self::XML_PATH_FPT_ENABLED, $store);
    }

    /**
     * Set the store for the current quote
     *
     * @param Mage_Core_Model_Store $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * Returns all summed weee taxes with all local taxes applied
     *
     * @throws Mage_Core_Exception
     * @param array $attributes Array of Varien_Object, result from getProductWeeeAttributes()
     * @return float
     */
    public function getAmountInclTaxes($attributes)
    {
        if (is_array($attributes)) {
            $amount = 0;
            foreach ($attributes as $attribute) {
                /** @var Varien_Object $attribute */
                $amount += $attribute->getAmount() + $attribute->getTaxAmount();
            }
        } else {
            // phpcs:ignore Ecg.Classes.ObjectInstantiation.DirectInstantiation
            throw new Mage_Core_Exception('$attributes must be an array');
        }

        return (float) $amount;
    }

    /**
     * Check if the configuration for the particular store causes conflicts
     *
     * @param Mage_Core_Model_Store|null $store
     * @return bool
     */
    public function validateCatalogPricesAndFptConfiguration($store = null)
    {
        // Check the configuration - Weee enabled and catalog display
        $priceIncludesTax = $this->_getHelper('tax')->priceIncludesTax($store);
        // $priceIncludesTax = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store);
        $fptTaxConfig = $this->getTaxType($store);

        // If FPT == Including tax & Catalog Prices Excluding Tax or
        // FPT = Taxed (Meaning - go ahead and calculate tax on fpt and Catalog Prices Include tax)
        return (($fptTaxConfig == Mage_Tax_Model_Config::FPT_LOADED_DISPLAY_WITH_TAX && !$priceIncludesTax)
            || ($fptTaxConfig == Mage_Tax_Model_Config::FPT_TAXED && $priceIncludesTax));
    }

    /**
     * Set a value to a specific property searching FPT by title for the Item
     *
     * @param Mage_Core_Model_Abstract $item
     * @param string $title
     * @param string $property
     * @param string $value
     */
    public function setWeeeTaxesAppliedProperty($item, $title, $property, $value)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        foreach ($weeeTaxAppliedAmounts as &$weeeTaxAppliedAmount) {
            //if the title is not set we set the value to all fields
            if (isset($title)) {
                if ($weeeTaxAppliedAmount['title'] == $title) {
                    $weeeTaxAppliedAmount[$property] = $value;
                }
            } else {
                $weeeTaxAppliedAmount[$property] = $value;
            }
        }

        $item->setWeeeTaxApplied(serialize($weeeTaxAppliedAmounts));
    }

    /**
     * Get the total weee tax
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['amount_incl_tax'], 0);
        }

        return $totalWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total base weee tax
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getBaseWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalBaseWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalBaseWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['base_amount_incl_tax'], 0);
        }

        return $totalBaseWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total weee including tax by row
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getRowWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['row_amount_incl_tax'], 0);
        }

        return $totalWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total base weee including tax by row
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getBaseRowWeeeTaxInclTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalWeeeTaxIncTaxApplied = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalWeeeTaxIncTaxApplied += max($weeeTaxAppliedAmount['base_row_amount_incl_tax'], 0);
        }

        return $totalWeeeTaxIncTaxApplied;
    }

    /**
     * Get the total tax applied on weee by unit
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getTotalTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['amount_incl_tax']
                - $weeeTaxAppliedAmount['amount'], 0);
        }

        return $totalTaxForWeeeTax;
    }

    /**
     * Get the total tax applied on weee by unit
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    public function getBaseTotalTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['base_amount_incl_tax']
                - $weeeTaxAppliedAmount['base_amount'], 0);
        }

        return $totalTaxForWeeeTax;
    }

    /**
     * Get the Total tax applied for Weee
     *
     * @param Mage_Core_Model_Abstract|Varien_Object $item
     * @return float
     */
    public function getTotalRowTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['row_amount_incl_tax']
                - $weeeTaxAppliedAmount['row_amount'], 0);
        }

        return $totalTaxForWeeeTax;
    }

    /**
     * Get the Total tax applied in base for Weee
     *
     * @param Mage_Core_Model_Abstract|Varien_Object $item
     * @return float
     */
    public function getBaseTotalRowTaxAppliedForWeeeTax($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $totalTaxForWeeeTax = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $totalTaxForWeeeTax += max($weeeTaxAppliedAmount['base_row_amount_incl_tax']
                - $weeeTaxAppliedAmount['base_row_amount'], 0);
        }

        return $totalTaxForWeeeTax;
    }

    /**
     * Calculate row weee amount for an order, invoice or credit memo item
     * The returned value may contain discount if the discount is not included in
     * the discount for subtotal
     *
     * @param mixed $item
     * @return float
     */
    public function getRowWeeeAmountAfterDiscount($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $weeeAmountInclDiscount = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $weeeAmountInclDiscount += $weeeTaxAppliedAmount['row_amount'];
            if (!$this->includeInSubtotal()) {
                $weeeAmountInclDiscount -= $weeeTaxAppliedAmount['weee_discount'] ?? 0;
            }
        }

        return $weeeAmountInclDiscount;
    }

    /**
     * Calculate base row weee amount for an order, invoice or credit memo item
     * The returned value may contain discount if the discount is not included in
     * the discount for subtotal
     *
     * @param mixed $item
     * @return float
     */
    public function getBaseRowWeeeAmountAfterDiscount($item)
    {
        $weeeTaxAppliedAmounts = $this->getApplied($item);
        $baseWeeeAmountInclDiscount = 0;
        foreach ($weeeTaxAppliedAmounts as $weeeTaxAppliedAmount) {
            $baseWeeeAmountInclDiscount += $weeeTaxAppliedAmount['base_row_amount'];
            if (!$this->includeInSubtotal()) {
                $baseWeeeAmountInclDiscount -= $weeeTaxAppliedAmount['base_weee_discount'] ?? 0;
            }
        }

        return $baseWeeeAmountInclDiscount;
    }

    /**
     * Get The Helper with the name provider
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }
}
