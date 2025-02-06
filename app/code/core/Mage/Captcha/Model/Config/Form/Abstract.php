<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Captcha
 */

/**
 * Data source to fill "Forms" field
 *
 * @category   Mage
 * @package    Mage_Captcha
 */
abstract class Mage_Captcha_Model_Config_Form_Abstract extends Mage_Core_Model_Config_Data
{
    /**
     * @var string
     */
    protected $_configPath;

    /**
     * Returns options for form multiselect
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        /** @var Mage_Core_Model_Config_Element $backendNode */
        $backendNode = Mage::getConfig()->getNode($this->_configPath);
        if ($backendNode) {
            foreach ($backendNode->children() as $formNode) {
                /** @var Mage_Core_Model_Config_Element $formNode */
                if (!empty($formNode->label)) {
                    $optionArray[] = ['label' => (string) $formNode->label, 'value' => $formNode->getName()];
                }
            }
        }
        return $optionArray;
    }
}
