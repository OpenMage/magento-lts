<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Base html block
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Block_Text_List_Link extends Mage_Core_Block_Text
{
    /**
     * @param array $liParams
     * @param array $aParams
     * @param string $innerText
     * @param string $afterText
     * @return $this
     */
    public function setLink($liParams, $aParams, $innerText, $afterText = '')
    {
        $this->setLiParams($liParams);
        $this->setAParams($aParams);
        $this->setInnerText($innerText);
        $this->setAfterText($afterText);

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
        $this->addText('><a');

        $params = $this->getAParams();
        if (!empty($params) && is_array($params)) {
            foreach ($params as $key => $value) {
                $this->addText(' ' . $key . '="' . addslashes($value) . '"');
            }
        } elseif (is_string($params)) {
            $this->addText(' ' . $params);
        }

        $this->addText('>' . $this->getInnerText() . '</a>' . $this->getAfterText() . '</li>' . "\r\n");

        return parent::_toHtml();
    }
}
