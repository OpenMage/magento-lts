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
 * @method string getTagContents()
 * @method getTagName()
 * @method array getTagParams()
 * @method $this setTagContents(string $value)
 * @method $this setTagParams(array $value)
 */
class Mage_Core_Block_Text_Tag extends Mage_Core_Block_Text
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTagParams([]);
    }

    /**
     * @param  array|string $param
     * @param  null|string  $value
     * @return $this
     */
    public function setTagParam($param, $value = null)
    {
        if (is_array($param) && is_null($value)) {
            foreach ($param as $k => $v) {
                $this->setTagParam($k, $v);
            }
        } else {
            $params = $this->getTagParams();
            $params[$param] = $value;
            $this->setTagParams($params);
        }

        return $this;
    }

    /**
     * @param  string $text
     * @return $this
     */
    public function setContents($text)
    {
        $this->setTagContents($text);
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $this->setText('<' . $this->getTagName() . ' ');
        if ($this->getTagParams()) {
            foreach ($this->getTagParams() as $k => $v) {
                $this->addText($k . '="' . $v . '" ');
            }
        }

        $this->addText('>' . $this->getTagContents() . '</' . $this->getTagName() . '>' . "\r\n");
        return parent::_toHtml();
    }
}
