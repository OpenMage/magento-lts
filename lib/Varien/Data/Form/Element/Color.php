<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Data
 */

/**
 * Form text element
 *
 * @package    Varien_Data
 */
class Varien_Data_Form_Element_Color extends Varien_Data_Form_Element_Abstract
{
    public const VALIDATION_REGEX_WITH_HASH = '/^#[a-f0-9]{6}$/i';

    public const VALIDATION_REGEX_WITHOUT_HASH = '/^[a-f0-9]{6}$/i';

    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
    }

    /**
     * @return array
     */
    public function getHtmlAttributes()
    {
        return ['type', 'title', 'class', 'style', 'oninput', 'disabled', 'readonly', 'tabindex'];
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $id = $this->getHtmlId();
        $with_hash = strtolower((string) ($this->original_data['with_hash'] ?? 1));

        if (!empty($with_hash) && $with_hash !== 'false' && $with_hash !== 'off') {
            $oninput = "document.getElementById('{$id}').value = this.value";
            $regex = self::VALIDATION_REGEX_WITH_HASH;
            $this->setOninput("document.getElementById('{$id}:html5').value = {$regex}.test(this.value) ? this.value : '#000000'");
            $this->addClass('validate-hex-color-hash');
        } else {
            $oninput = "document.getElementById('{$id}').value = this.value.substring(1)";
            $regex = self::VALIDATION_REGEX_WITHOUT_HASH;
            $this->setOninput("document.getElementById('{$id}:html5').value = {$regex}.test(this.value) ? '#'+this.value : '#000000'");
            $this->addClass('validate-hex-color');
        }

        $html = '<input type="color" id="' . $id . ':html5" class="input-color-html5" '
            . 'value="#' . trim($this->getEscapedValue(), '#') . '" oninput="' . $oninput . '" '
            . '/>' . "\n";

        $this->addClass('input-color');
        return $html . parent::getElementHtml();
    }
}
