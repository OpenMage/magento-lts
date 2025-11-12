<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Catalog product option model
 *
 * @package    Mage_Catalog
 *
 * @method Mage_Catalog_Model_Resource_Product_Option _getResource()
 * @method bool getAddRequiredFilter()
 * @method bool getAddRequiredFilterValue()
 * @method Mage_Catalog_Model_Resource_Product_Option_Collection getCollection()
 * @method string getFileExtension()
 * @method int getImageSizeX()
 * @method int getImageSizeY()
 * @method int getIsRequire()
 * @method int getMaxCharacters()
 * @method null|int getOptionId()
 * @method string getPriceType()
 * @method null|string getProductId()
 * @method Mage_Catalog_Model_Resource_Product_Option getResource()
 * @method Mage_Catalog_Model_Resource_Product_Option_Collection getResourceCollection()
 * @method string getSku()
 * @method int getSortOrder()
 * @method int getStoreId()
 * @method float getStorePrice()
 * @method string getStoreTitle()
 * @method string getTitle()
 * @method string getType()
 * @method $this setFileExtension(string $value)
 * @method $this setImageSizeX(int $value)
 * @method $this setImageSizeY(int $value)
 * @method $this setIsRequire(int $value)
 * @method $this setMaxCharacters(int $value)
 * @method $this setOptionId(null|int $value)
 * @method $this setProductId(null|string $value)
 * @method $this setSku(string $value)
 * @method $this setSortOrder(int $value)
 * @method $this setType(string $value)
 */
class Mage_Catalog_Model_Product_Option extends Mage_Core_Model_Abstract
{
    /**
     * Option group text
     */
    public const OPTION_GROUP_TEXT   = 'text';

    /**
     * Option group file
     */
    public const OPTION_GROUP_FILE   = 'file';

    /**
     * Option group select
     */
    public const OPTION_GROUP_SELECT = 'select';

    /**
     * Option group date
     */
    public const OPTION_GROUP_DATE   = 'date';

    /**
     * Option type field
     */
    public const OPTION_TYPE_FIELD     = 'field';

    /**
     * Option type area
     */
    public const OPTION_TYPE_AREA      = 'area';

    /**
     * Option group file
     */
    public const OPTION_TYPE_FILE      = 'file';

    /**
     * Option type drop down
     */
    public const OPTION_TYPE_DROP_DOWN = 'drop_down';

    /**
     * Option type radio
     */
    public const OPTION_TYPE_RADIO     = 'radio';

    /**
     * Option type checkbox
     */
    public const OPTION_TYPE_CHECKBOX  = 'checkbox';

    /**
     * Option type multiple
     */
    public const OPTION_TYPE_MULTIPLE  = 'multiple';

    /**
     * Option type date
     */
    public const OPTION_TYPE_DATE      = 'date';

    /**
     * Option type date/time
     */
    public const OPTION_TYPE_DATE_TIME = 'date_time';

    /**
     * Option type time
     */
    public const OPTION_TYPE_TIME      = 'time';

    /**
     * Product instance
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Options
     *
     * @var array
     */
    protected $_options = [];

    /**
     * Value instance
     *
     * @var Mage_Catalog_Model_Product_Option_Value
     */
    protected $_valueInstance;

    /**
     * Values
     *
     * @var array
     */
    protected $_values = [];

    protected function _construct()
    {
        $this->_init('catalog/product_option');
    }

    /**
     * Add value of option to values array
     *
     * @return $this
     */
    public function addValue(Mage_Catalog_Model_Product_Option_Value $value)
    {
        $this->_values[$value->getId()] = $value;
        return $this;
    }

    /**
     * Get value by given id
     *
     * @param int|string $valueId
     * @return Mage_Catalog_Model_Product_Option_Value
     */
    public function getValueById($valueId)
    {
        return $this->_values[$valueId] ?? null;
    }

    /**
     * Get values
     *
     * @return Mage_Catalog_Model_Product_Option_Value[]
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Retrieve value instance
     *
     * @return Mage_Catalog_Model_Product_Option_Value
     */
    public function getValueInstance()
    {
        if (!$this->_valueInstance) {
            $this->_valueInstance = Mage::getSingleton('catalog/product_option_value');
        }

        return $this->_valueInstance;
    }

    /**
     * Add option for save it
     *
     * @param array $option
     * @return $this
     */
    public function addOption($option)
    {
        $this->_options[] = $option;
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Set options for array
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Set options to empty array
     *
     * @return $this
     */
    public function unsetOptions()
    {
        $this->_options = [];
        return $this;
    }

    /**
     * Retrieve product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * Set product instance
     *
     * @return $this
     */
    public function setProduct(?Mage_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * Get group name of option by given option type
     *
     * @param string $type
     * @return string
     */
    public function getGroupByType($type = null)
    {
        if (is_null($type)) {
            $type = $this->getType();
        }

        $optionGroupsToTypes = [
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_DATE => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_DATE_TIME => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_TIME => self::OPTION_GROUP_DATE,
        ];

        return $optionGroupsToTypes[$type] ?? '';
    }

    /**
     * Group model factory
     *
     * @param string $type Option type
     * @return Mage_Catalog_Model_Product_Option_Type_Default
     */
    public function groupFactory($type)
    {
        $group = $this->getGroupByType($type);
        if (!empty($group)) {
            /** @var Mage_Catalog_Model_Product_Option_Type_Default $model */
            $model = Mage::getModel('catalog/product_option_type_' . $group);
            return $model;
        }

        Mage::throwException(Mage::helper('catalog')->__('Wrong option type to get group instance.'));
    }

    /**
     * Save options.
     *
     * @return $this
     */
    public function saveOptions()
    {
        foreach ($this->getOptions() as $option) {
            $this->setData($option)
                ->setData('product_id', $this->getProduct()->getId())
                ->setData('store_id', $this->getProduct()->getStoreId());

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }

            $isEdit = (bool) $this->getId() ? true : false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');

                    /**
                     * if previous option has different group from one is came now
                     * need to remove all data of previous group
                     */
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {
                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }

                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }

                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }
                    }
                }

                // phpcs:ignore Ecg.Performance.Loop.ModelLSD
                $this->save();
            }
        }

        //eof foreach()
        return $this;
    }

    /**
     * After save
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        $this->getValueInstance()->unsetValues();
        if (is_array($this->getData('values'))) {
            foreach ($this->getData('values') as $value) {
                $this->getValueInstance()->addValue($value);
            }

            $this->getValueInstance()->setOption($this)
                ->saveValues();
        } elseif ($this->getGroupByType($this->getType()) == self::OPTION_GROUP_SELECT) {
            Mage::throwException(Mage::helper('catalog')->__('Select type options required values rows.'));
        }

        return parent::_afterSave();
    }

    /**
     * Return price. If $flag is true and price is percent
     *  return converted percent to price
     *
     * @param bool $flag
     * @return float
     */
    public function getPrice($flag = false)
    {
        if ($flag && $this->getPriceType() == 'percent') {
            $basePrice = $this->getProduct()->getFinalPrice();
            return $basePrice * ($this->_getData('price') / 100);
        }

        return $this->_getData('price');
    }

    /**
     * Delete prices of option
     *
     * @param int|string $optionId
     * @return $this
     */
    public function deletePrices($optionId)
    {
        $this->getResource()->deletePrices($optionId);
        return $this;
    }

    /**
     * Delete titles of option
     *
     * @param int|string $optionId
     * @return $this
     */
    public function deleteTitles($optionId)
    {
        $this->getResource()->deleteTitles($optionId);
        return $this;
    }

    /**
     * get Product Option Collection
     *
     * @return Mage_Catalog_Model_Resource_Product_Option_Collection
     */
    public function getProductOptionCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('product_id', $product->getId())
            ->addTitleToResult($product->getStoreId())
            ->addPriceToResult($product->getStoreId())
            ->setOrder('sort_order', 'asc')
            ->setOrder('title', 'asc');

        if ($this->getAddRequiredFilter()) {
            $collection->addRequiredFilter($this->getAddRequiredFilterValue());
        }

        $collection->addValuesToResult($product->getStoreId());
        return $collection;
    }

    /**
     * Get collection of values for current option
     *
     * @return Mage_Catalog_Model_Resource_Product_Option_Value_Collection
     */
    public function getValuesCollection()
    {
        return $this->getValueInstance()
            ->getValuesCollection($this);
    }

    /**
     * Get collection of values by given option ids
     *
     * @param array $optionIds
     * @param int $storeId
     * @return Mage_Catalog_Model_Resource_Product_Option_Value_Collection
     */
    public function getOptionValuesByOptionId($optionIds, $storeId)
    {
        return Mage::getModel('catalog/product_option_value')
            ->getValuesByOption($optionIds, $this->getId(), $storeId);
    }

    /**
     * Prepare array of options for duplicate
     *
     * @return array
     */
    public function prepareOptionForDuplicate()
    {
        $this->setProductId(null);
        $this->setOptionId(null);
        $newOption = $this->__toArray();
        $values = $this->getValues();
        if ($values) {
            $newValuesArray = [];
            foreach ($values as $value) {
                $newValuesArray[] = $value->prepareValueForDuplicate();
            }

            $newOption['values'] = $newValuesArray;
        }

        return $newOption;
    }

    /**
     * Duplicate options for product
     *
     * @param int $oldProductId
     * @param int $newProductId
     * @return $this
     */
    public function duplicate($oldProductId, $newProductId)
    {
        $this->getResource()->duplicate($this, $oldProductId, $newProductId);

        return $this;
    }

    /**
     * Retrieve option searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()->getSearchableData($productId, $storeId);
    }

    /**
     * Clearing object's data
     *
     * @return $this
     */
    protected function _clearData()
    {
        $this->_data = [];
        $this->_values = [];
        return $this;
    }

    /**
     * Clearing cyclic references
     *
     * @return $this
     */
    protected function _clearReferences()
    {
        foreach ($this->_values as $value) {
            $value->unsetOption();
        }

        return $this;
    }

    /**
     * Check whether custom option could have multiple values
     *
     * @return bool
     */
    public function isMultipleType()
    {
        return match ($this->getType()) {
            self::OPTION_TYPE_MULTIPLE, self::OPTION_TYPE_CHECKBOX => true,
            default => false,
        };
    }
}
