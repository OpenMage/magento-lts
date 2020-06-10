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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Serializer extends Mage_Core_Block_Template
{

    /**
     * Store grid input names to serialize
     *
     * @var array
     */
    private $_inputsToSerialize = array();

    /**
     * Set serializer template
     *
     * @return $this
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/grid/serializer.phtml');
        return $this;
    }

    /**
     * Register grid column input name to serialize
     *
     * @param string $name
     */
    public function addColumnInputName($names)
    {
        if (is_array($names)) {
            foreach ($names as $name) {
                $this->addColumnInputName($name);
            }
        }
        else {
            if (!in_array($names, $this->_inputsToSerialize)) {
                $this->_inputsToSerialize[] = $names;
            }
        }
    }

    /**
     * Get grid column input names to serialize
     *
     * @return unknown
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
        $result = array();
        if ($serializeData = $this->getSerializeData()) {
            $result = $serializeData;
        }
        elseif (!empty($this->_inputsToSerialize)) {
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
     *
     * @param Mage_Adminhtml_Block_Widget_Grid | string $grid grid object or grid block name
     * @param string $callback block method  to retrieve data to serialize
     * @param string $hiddenInputName hidden input name where serialized data will be store
     * @param string $reloadParamName name of request parametr that will be used to save setted data while reload grid
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
