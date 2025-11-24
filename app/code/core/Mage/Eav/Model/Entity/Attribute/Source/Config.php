<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Eav
 */

/**
 * Entity/Attribute/Model - attribute selection source from configuration
 *
 * This class should be abstract, but kept usual for legacy purposes
 *
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity_Attribute_Source_Config extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Config Node Path
     *
     * @var Mage_Core_Model_Config_Element
     */
    protected $_configNodePath;

    /**
     * Retrieve all options for the source from configuration
     *
     * @return array
     * @throws Mage_Eav_Exception
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [];
            $rootNode = null;
            if ($this->_configNodePath) {
                $rootNode = Mage::getConfig()->getNode($this->_configNodePath);
            }

            if (!$rootNode) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Failed to load node %s from config', $this->_configNodePath));
            }

            $options = $rootNode->children();
            if (empty($options)) {
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('No options found in config node %s', $this->_configNodePath));
            }

            foreach ($options as $option) {
                $this->_options[] = [
                    'value' => (string) $option->value,
                    'label' => Mage::helper('eav')->__((string) $option->label),
                ];
            }
        }

        return $this->_options;
    }
}
