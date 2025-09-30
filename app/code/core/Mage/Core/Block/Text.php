<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Base html block
 *
 * @package    Mage_Core
 *
 * @method array getLiParams()
 * @method $this setLiParams(array $value)
 * @method array getAParams()
 * @method $this setAParams(array $value)
 * @method string getInnerText()
 * @method $this setInnerText(string $value)
 * @method string getAfterText()
 * @method $this setAfterText(string $value)
 */
class Mage_Core_Block_Text extends Mage_Core_Block_Abstract
{
    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->setData('text', $text);
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->getData('text');
    }

    /**
     * @param string $text
     * @param bool $before
     */
    public function addText($text, $before = false)
    {
        if ($before) {
            $this->setText($text . $this->getText());
        } else {
            $this->setText($this->getText() . $text);
        }
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }

        return $this->getText();
    }
}
