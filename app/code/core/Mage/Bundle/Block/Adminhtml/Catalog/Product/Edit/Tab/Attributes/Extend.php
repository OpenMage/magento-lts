<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Extended Attributes Block
 *
 * @package    Mage_Bundle
 *
 * @method bool getCanEditPrice()
 * @method $this setCanEditPrice(bool $true)
 * @method bool getCanReadPrice()
 * @method $this setCanReadPrice(bool $true)
 * @method string getDefaultProductPrice()
 * @method bool getDisableChild()
 * @method $this setDisableChild(bool $value)
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes_Extend extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{
    public const DYNAMIC = 0;

    public const FIXED = 1;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Get Element Html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $elementHtml = parent::getElementHtml();

        $switchAttributeCode = $this->getAttribute()->getAttributeCode() . '_type';
        $switchAttributeValue = $this->getProduct()->getData($switchAttributeCode);

        $html = '<select name="product[' . $switchAttributeCode . ']" id="' . $switchAttributeCode
        . '" type="select" class="required-entry select next-toinput"'
        . ($this->getProduct()->getId() && $this->getAttribute()->getAttributeCode() == 'price'
            || $this->getElement()->getReadonly() ? ' disabled="disabled"' : '') . '>
            <option value="">' . $this->__('-- Select --') . '</option>
            <option ' . ($switchAttributeValue == self::DYNAMIC ? 'selected' : '')
            . ' value="' . self::DYNAMIC . '">' . $this->__('Dynamic') . '</option>
            <option ' . ($switchAttributeValue == self::FIXED ? 'selected' : '')
            . ' value="' . self::FIXED . '">' . $this->__('Fixed') . '</option>
        </select>';

        if (!($this->getAttribute()->getAttributeCode() == 'price'
            && $this->getCanReadPrice() === false)
        ) {
            $html .= '<span class="next-toselect">' . $elementHtml . '</span>';
        }

        if ($this->getDisableChild() && !$this->getElement()->getReadonly()) {
            $html .= '<script type="text/javascript">
                function ' . $switchAttributeCode . "_change() {
                    if ($('" . $switchAttributeCode . "').value == '" . self::DYNAMIC . "') {
                        if ($('" . $this->getAttribute()->getAttributeCode() . "')) {
                            $('" . $this->getAttribute()->getAttributeCode() . "').disabled = true;
                            $('" . $this->getAttribute()->getAttributeCode() . "').value = '';
                            $('" . $this->getAttribute()->getAttributeCode() . "').removeClassName('required-entry');
                        }

                        if ($('dynamic-price-warrning')) {
                            $('dynamic-price-warrning').show();
                        }
                    } else {
                        if ($('" . $this->getAttribute()->getAttributeCode() . "')) {";

            if ($this->getAttribute()->getAttributeCode() == 'price'
                && $this->getCanEditPrice() === false
                && $this->getCanReadPrice() === true
                && $this->getProduct()->isObjectNew()
            ) {
                $defaultProductPrice = ($this->getDefaultProductPrice()) ? $this->getDefaultProductPrice() : "''";
                $html .= "$('" . $this->getAttribute()->getAttributeCode() . "').value = " . $defaultProductPrice . ';';
            } else {
                $html .= "$('" . $this->getAttribute()->getAttributeCode() . "').disabled = false;
                          $('" . $this->getAttribute()->getAttributeCode() . "').addClassName('required-entry');";
            }

            $html .= "}

                        if ($('dynamic-price-warrning')) {
                            $('dynamic-price-warrning').hide();
                        }
                    }
                }";

            if (!($this->getAttribute()->getAttributeCode() == 'price'
                && !$this->getCanEditPrice()
                && !$this->getProduct()->isObjectNew())
            ) {
                $html .= "$('" . $switchAttributeCode . "').observe('change', " . $switchAttributeCode . '_change);';
            }

            $html .= $switchAttributeCode . '_change();
            </script>';
        }

        return $html;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->getData('product')) {
            $this->setData('product', Mage::registry('product'));
        }

        return $this->getData('product');
    }
}
