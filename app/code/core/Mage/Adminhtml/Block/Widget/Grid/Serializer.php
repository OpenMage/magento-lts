<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Serializer extends Mage_Core_Block_Template
{
    /**
     * Store grid input names to serialize
     *
     * @var array
     */
    private $_inputsToSerialize = [];

    /**
     * Set serializer template
     *
     * @return $this
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/grid/serializer.phtml');
        return $this;
    }

    /**
     * Register grid column input name to serialize
     *
     * @param array|string $names
     */
    public function addColumnInputName($names)
    {
        if (is_array($names)) {
            foreach ($names as $name) {
                $this->addColumnInputName($name);
            }
        } elseif (!in_array($names, $this->_inputsToSerialize)) {
            $this->_inputsToSerialize[] = $names;
        }
    }

    /**
     * Get grid column input names to serialize
     *
     * @param  bool         $asJSON
     * @return array|string
     */
    public function getColumnInputNames($asJSON = false)
    {
        if ($asJSON) {
            return Mage::helper('core')->jsonEncode($this->_inputsToSerialize);
        }

        return $this->_inputsToSerialize;
    }

    /**
     * Get object data as JSON
     *
     * @return string
     */
    public function getDataAsJSON()
    {
        $result = [];
        if ($serializeData = $this->getSerializeData()) {
            $result = $serializeData;
        } elseif (!empty($this->_inputsToSerialize)) {
            return '{}';
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    /**
     * Initialize grid block
     *
     * Get grid block from layout by specified block name
     * Get serialize data to manage it (called specified method, that return data to manage)
     * Also use reload param name for saving grid checked boxes states
     *
     * @param Mage_Adminhtml_Block_Widget_Grid|string $grid            grid object or grid block name
     * @param string                                  $callback        block method  to retrieve data to serialize
     * @param string                                  $hiddenInputName hidden input name where serialized data will be store
     * @param string                                  $reloadParamName name of request parameter that will be used to save set data while reload grid
     */
    public function initSerializerBlock($grid, $callback, $hiddenInputName, $reloadParamName = 'entityCollection')
    {
        if (is_string($grid)) {
            $grid = $this->getLayout()->getBlock($grid);
        }

        if ($grid instanceof Mage_Adminhtml_Block_Widget_Grid) {
            $this->setGridBlock($grid)
                 ->setInputElementName($hiddenInputName)
                 ->setReloadParamName($reloadParamName)
                 ->setSerializeData($grid->$callback());
        }
    }
}
