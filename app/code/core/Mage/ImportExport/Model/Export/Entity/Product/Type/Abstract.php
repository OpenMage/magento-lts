<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ImportExport
 */

/**
 * Export entity product type abstract model
 *
 * @package    Mage_ImportExport
 */
abstract class Mage_ImportExport_Model_Export_Entity_Product_Type_Abstract
{
    /**
     * Overridden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = [];

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = [];

    /**
     * Attributes with index (not label) value.
     *
     * @var array
     */
    protected $_indexValueAttributes = [];

    /**
     * Return disabled attributes codes.
     *
     * @return array
     */
    public function getDisabledAttrs()
    {
        return $this->_disabledAttrs;
    }

    /**
     * Get attribute codes with index (not label) value.
     *
     * @return array
     */
    public function getIndexValueAttributes()
    {
        return $this->_indexValueAttributes;
    }

    /**
     * Additional check for model availability. If method returns FALSE - model is not suitable for data processing.
     *
     * @return bool
     */
    public function isSuitable()
    {
        return true;
    }

    /**
     * Add additional data to attribute.
     *
     * @return bool
     */
    public function overrideAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        if (!empty($this->_attributeOverrides[$attribute->getAttributeCode()])) {
            $data = $this->_attributeOverrides[$attribute->getAttributeCode()];

            if (isset($data['options_method']) && method_exists($this, $data['options_method'])) {
                $data['filter_options'] = $this->{$data['options_method']}();
            }
            $attribute->addData($data);

            return true;
        }
        return false;
    }
}
