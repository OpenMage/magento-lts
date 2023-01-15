<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form text element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Color extends Varien_Data_Form_Element_Abstract
{
    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('color');
        $this->setExtType('textfield');
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'title', 'class', 'style', 'disabled', 'readonly', 'tabindex'];
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $id = $this->getHtmlId();
        $valueWithoutHash = ltrim($this->getEscapedValue(), '#');
        $with_hash = (bool) ($this->original_data['with_hash'] ?? 1);

        $onchange = "document.getElementById('{$id}').value=this.value";
        if (!$with_hash) {
            $onchange .= '.substring(1)';
        }

        $html = '<input id="' . $id . '" type="hidden" name="' . $this->getName()
            . '" value="' . $valueWithoutHash . '" ' . '/>' . "\n";
        $html .= '<input value="#' . $valueWithoutHash . '" ' . $this->serialize($this->getHtmlAttributes())
            . 'onchange="' . $onchange .  '" ' . '/>' . "\n";
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}
