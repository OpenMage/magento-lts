<?php
/**
 * OpenMage
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
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Price display type source model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tax_Model_System_Config_Source_Tax_Display_Type
{
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];
            $this->_options[] = ['value'=>Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX, 'label'=>Mage::helper('tax')->__('Excluding Tax')];
            $this->_options[] = ['value'=>Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX, 'label'=>Mage::helper('tax')->__('Including Tax')];
            $this->_options[] = ['value'=>Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH, 'label'=>Mage::helper('tax')->__('Including and Excluding Tax')];
        }
        return $this->_options;
    }
}
