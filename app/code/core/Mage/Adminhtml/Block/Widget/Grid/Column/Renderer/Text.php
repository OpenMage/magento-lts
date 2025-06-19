<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml grid item renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Format variables pattern
     *
     * @var string
     */
    protected $_variablePattern = '/\\$([a-z0-9_]+)/i';

    /**
     * Renders grid column
     *
     * @return mixed
     */
    public function _getValue(Varien_Object $row)
    {
        $format = ($this->getColumn()->getFormat()) ? $this->getColumn()->getFormat() : null;
        $defaultValue = $this->getColumn()->getDefault();
        if (is_null($format)) {
            // If no format and it column not filtered specified return data as is.
            $data = parent::_getValue($row);
            $string = $data ?? $defaultValue ?? '';
            return $this->escapeHtml($string);
        } elseif (preg_match_all($this->_variablePattern, $format, $matches)) {
            // Parsing of format string
            $formattedString = $format;
            foreach ($matches[0] as $matchIndex => $match) {
                $value = $this->escapeHtml($row->getData($matches[1][$matchIndex]));
                $formattedString = str_replace($match, $value, $formattedString);
            }
            return $formattedString;
        } else {
            return $this->escapeHtml($format);
        }
    }
}
