<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form hidden element
 *
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
