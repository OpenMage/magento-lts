<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Bundle
 */

/**
 * Bundle Option Model
 *
 * @package    Mage_Bundle
 *
 * @method Mage_Bundle_Model_Resource_Option            _getResource()
 * @method Mage_Bundle_Model_Resource_Option_Collection getCollection()
 * @method string                                       getDefaultTitle()
 * @method Mage_Bundle_Model_Resource_Option            getResource()
 * @method Mage_Bundle_Model_Resource_Option_Collection getResourceCollection()
 * @method Mage_Catalog_Model_Product[]                 getSelections()
 * @method string                                       getTitle()
 * @method $this                                        setSelections(array $value)
 */
class Mage_Bundle_Model_Option extends Mage_Core_Model_Abstract
{
    /**
     * Default selection object
     *
     * @var Mage_Bundle_Model_Selection
     */
    protected $_defaultSelection = null;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('bundle/option');
        parent::_construct();
    }

    public function getParentId(): int
    {
        return (int) $this->_getData('parent_id');
    }

    public function getPosition(): int
    {
        return (int) $this->_getData('position');
    }

    public function getRequired(): int
    {
        return (int) $this->_getData('required');
    }

    public function getType(): string
    {
        return (string) $this->_getData('type');
    }

    public function setParentId(int $value): static
    {
        return $this->setData('parent_id', $value);
    }

    public function setPosition(int $value): static
    {
        return $this->setData('position', $value);
    }

    public function setRequired(int $value): static
    {
        return $this->setData('required', $value);
    }

    public function setType(string $value): static
    {
        return $this->setData('type', $value);
    }

    /**
     * Add selection to option
     *
     * @param  Mage_Bundle_Model_Selection $selection
     * @return $this|false
     */
    public function addSelection($selection)
    {
        if (!$selection) {
            return false;
        }

        if (!$selections = $this->getDataByKey('selections')) {
            $selections = [];
        }

        $selections[] = $selection;
        $this->setSelections($selections);
        return $this;
    }

    /**
     * Check Is Saleable Option
     *
     * @return bool
     */
    public function isSaleable()
    {
        $saleable = 0;
        if ($this->getSelections()) {
            foreach ($this->getSelections() as $selection) {
                if ($selection->isSaleable()) {
                    $saleable++;
                }
            }

            return (bool) $saleable;
        }

        return false;
    }

    /**
     * Retrieve default Selection object
     *
     * @return Mage_Bundle_Model_Selection
     */
    public function getDefaultSelection()
    {
        if (!$this->_defaultSelection && $this->getSelections()) {
            foreach ($this->getSelections() as $selection) {
                if ($selection->getIsDefault()) {
                    $this->_defaultSelection = $selection;
                    break;
                }
            }
        }

        return $this->_defaultSelection;
    }

    /**
     * Check is multi Option selection
     *
     * @return bool
     */
    public function isMultiSelection()
    {
        if ($this->getType() === 'checkbox') {
            return true;
        }

        return $this->getType() === 'multi';
    }

    /**
     * Retrieve options searchable data
     *
     * @param  int   $productId
     * @param  int   $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()
            ->getSearchableData($productId, $storeId);
    }

    /**
     * Return selection by it's id
     *
     * @param  int                              $selectionId
     * @return false|Mage_Catalog_Model_Product
     */
    public function getSelectionById($selectionId)
    {
        $selections = $this->getSelections();
        $i = count($selections);

        while ($i-- && $selections[$i]->getSelectionId() != $selectionId) {
        }

        return $i == -1 ? false : $selections[$i];
    }

    public function getOptionId(): int
    {
        return (int) $this->_getData('option_id');
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }
}
