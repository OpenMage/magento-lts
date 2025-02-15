<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base html block
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Block_Text extends Mage_Core_Block_Abstract
{
    public function getAfterText(): ?string
    {
        return $this->getData('after_text');
    }

    /**
     * @return $this
     */
    public function setAfterText(?string $text)
    {
        return $this->setData('after_text', $text);
    }

    public function getInnerText(): ?string
    {
        return $this->getData('inner_text');
    }

    /**
     * @return $this
     */
    public function setInnerText(?string $text)
    {
        return $this->setData('inner_text', $text);
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
     * @return $this
     */
    public function setText($text)
    {
        $this->setData('text', $text);
        return $this;
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
     * @return string|array
     */
    public function getAParams()
    {
        return $this->getData('a_params');
    }

    /**
     * @param string|array $params
     * @return $this
     */
    public function setAParams($params)
    {
        return $this->setData('a_params', $params);
    }

    /**
     * @return string|array
     */
    public function getLiParams()
    {
        return $this->getData('li_params');
    }

    /**
     * @param string|array $params
     * @return $this
     */
    public function setLiParams($params)
    {
        return $this->setData('li_params', $params);
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
