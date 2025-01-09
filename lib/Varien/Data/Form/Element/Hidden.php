<?php

/**
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form hidden element
 *
 * @category   Varien
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Hidden extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Hidden constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('hidden');
        $this->setExtType('hiddenfield');
    }

    /**
     * @return mixed|string
     */
    public function getDefaultHtml()
    {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = $this->getElementHtml();
        }
        return $html;
    }
}
