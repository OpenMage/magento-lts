<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Info extends Varien_Data_Form_Element_Abstract
{
    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this
            ->setType('info')
            ->unsScope()
            ->unsCanUseDefaultValue()
            ->unsCanUseWebsiteValue();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $id = $this->getHtmlId();
        $label = $this->getLabel();
        return '<tr class="' . $id . '"><td class="label" colspan="99"><label>' . $label . '</label></td></tr>';
    }
}
