<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * HTML anchor element block
 *
 * @package    Mage_Core
 */
class Mage_Core_Block_Html_Link extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('core/link.phtml');
        parent::_construct();
    }
    /**
     * Prepare link attributes as serialized and formatted string
     *
     * @return string
     */
    public function getLinkAttributes()
    {
        $allow = [
            'href', 'title', 'charset', 'name', 'hreflang', 'rel', 'rev', 'accesskey', 'shape',
            'coords', 'tabindex', 'onfocus', 'onblur', // %attrs
            'id', 'class', 'style', // %coreattrs
            'lang', 'dir', // %i18n
            'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover', 'onmousemove',
            'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup', // %events
        ];

        $attributes = [];
        foreach ($allow as $attribute) {
            $value = $this->getDataUsingMethod($attribute);
            if (!is_null($value)) {
                $attributes[$attribute] = $this->escapeHtml($value);
            }
        }

        if (!empty($attributes)) {
            return $this->serialize($attributes);
        }
        return '';
    }

    /**
     * serialize attributes
     *
     * @param   array $attributes
     * @param   string $valueSeparator
     * @param   string $fieldSeparator
     * @param   string $quote
     * @return  string
     */
    public function serialize($attributes = [], $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        $res  = '';
        $data = [];

        foreach ($attributes as $key => $value) {
            $data[] = $key . $valueSeparator . $quote . $value . $quote;
        }
        return implode($fieldSeparator, $data);
    }
}
