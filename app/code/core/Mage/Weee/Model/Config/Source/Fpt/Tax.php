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
class Mage_Weee_Model_Config_Source_Fpt_Tax
{
    /**
     * Array of options for FPT Tax Configuration
     *
     * @return array
     */
    public function toOptionArray()
    {
        $weeeHelper = $this->_getHelper('weee');
        return [
            ['value' => 0, 'label' => $weeeHelper->__('Not Taxed')],
            ['value' => 1, 'label' => $weeeHelper->__('Taxed')],
            ['value' => 2, 'label' => $weeeHelper->__('Loaded and Displayed with Tax')],
        ];
    }

    /**
     * Return helper corresponding to given name
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }
}
