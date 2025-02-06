<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 *
 * @method string getContentType()
 * @method $this setContentType(string $value)
 * @method string getTitle()
 * @method string getDescription()
 * @method string getKeywords()
 * @method string getRobots()
 */
class Mage_Core_Block_Text_Tag_Meta extends Mage_Core_Block_Text
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getContentType()) {
            $this->setContentType('text/html; charset=utf-8');
        }
        $this->addText('<meta http-equiv="Content-Type" content="' . $this->getContentType() . '"/>' . "\n");
        $this->addText('<title>' . $this->getTitle() . '</title>' . "\n");
        $this->addText('<meta name="title" content="' . $this->getTitle() . '"/>' . "\n");
        $this->addText('<meta name="description" content="' . $this->getDescription() . '"/>' . "\n");
        $this->addText('<meta name="keywords" content="' . $this->getKeywords() . '"/>' . "\n");
        $this->addText('<meta name="robots" content="' . $this->getRobots() . '"/>' . "\n");

        return parent::_toHtml();
    }
}
