<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * List item block
 *
 * @package    Mage_Core
 */
class Mage_Core_Block_Text_List_Item extends Mage_Core_Block_Text
{
    /**
     * @param array $liParams
     * @param array $innerText
     * @return $this
     */
    public function setLink($liParams, $innerText)
    {
        $this->setLiParams($liParams);
        $this->setInnerText($innerText);

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $this->setText('<li');
        $params = $this->getLiParams();
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->addText(' ' . $key . '="' . addslashes($value) . '"');
            }
        } elseif (is_string($params)) {
            $this->addText(' ' . $params);
        }
        $this->addText('>' . $this->getInnerText() . '</li>' . "\r\n");

        return parent::_toHtml();
    }
}
