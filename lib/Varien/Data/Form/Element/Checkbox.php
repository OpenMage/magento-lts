<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form checkbox element
 *
 * @package    Varien_Data
 *
 * @method bool getChecked()
 */
class Varien_Data_Form_Element_Checkbox extends Varien_Data_Form_Element_Abstract
{
    /**
     * Varien_Data_Form_Element_Checkbox constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'title', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled', 'tabindex'];
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        if ($checked = $this->getChecked()) {
            $this->setData('checked', true);
        } else {
            $this->unsetData('checked');
        }

        return parent::getElementHtml();
    }

    /**
     * Set check status of checkbox
     *
     * @param  bool                              $value
     * @return Varien_Data_Form_Element_Checkbox
     */
    public function setIsChecked($value = false)
    {
        $this->setData('checked', $value);
        return $this;
    }

    /**
     * Return check status of checkbox
     *
     * @return bool
     */
    public function getIsChecked()
    {
        return $this->getData('checked');
    }
}
