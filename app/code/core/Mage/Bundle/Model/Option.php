<?php

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
 * @method Mage_Bundle_Model_Resource_Option _getResource()
 * @method Mage_Bundle_Model_Resource_Option getResource()
 * @method Mage_Bundle_Model_Resource_Option_Collection getCollection()
 * @method Mage_Bundle_Model_Resource_Option_Collection getResourceCollection()
 *
 * @method string getDefaultTitle()
 * @method int getOptionId()
 * @method int getParentId()
 * @method $this setParentId(int $value)
 * @method int getPosition()
 * @method $this setPosition(int $value)
 * @method int getRequired()
 * @method $this setRequired(int $value)
 * @method Mage_Catalog_Model_Product[] getSelections()
 * @method $this setSelections(array $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method string getType()
 * @method $this setType(string $value)
 */
class Mage_Bundle_Model_Option extends Mage_Core_Model_Abstract
{
    /**
     * Default selection object
     *
     * @var Mage_Bundle_Model_Selection
     */
    protected $_defaultSelection = null;

    protected function _construct()
    {
        $this->_init('bundle/option');
        parent::_construct();
    }

    /**
     * Add selection to option
     *
     * @param Mage_Bundle_Model_Selection $selection
     * @return $this|false
     */
    public function addSelection($selection)
    {
        if (!$selection) {
            return false;
        }

        if (!$selections = $this->getData('selections')) {
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
        } else {
            return false;
        }
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
        /**
         *         if (!$this->_defaultSelection && $this->getSelections()) {
            $_selections = array();
            foreach ($this->getSelections() as $selection) {
                if ($selection->getIsDefault()) {
                    $_selections[] = $selection;
                }
            }
            if (!empty($_selections)) {
                $this->_defaultSelection = $_selections;
            } else {
                return null;
            }
        }
        return $this->_defaultSelection;
         */
    }

    /**
     * Check is multi Option selection
     *
     * @return bool
     */
    public function isMultiSelection()
    {
        if ($this->getType() == 'checkbox' || $this->getType() == 'multi') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve options searchable data
     *
     * @param int $productId
     * @param int $storeId
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
     * @param int $selectionId
     * @return Mage_Catalog_Model_Product|false
     */
    public function getSelectionById($selectionId)
    {
        $selections = $this->getSelections();
        $i = count($selections);

        while ($i-- && $selections[$i]->getSelectionId() != $selectionId) {
        }

        return $i == -1 ? false : $selections[$i];
    }
}
