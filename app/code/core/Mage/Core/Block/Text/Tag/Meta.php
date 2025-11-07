<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 *
 * @method string getContentType()
 * @method string getDescription()
 * @method string getKeywords()
 * @method string getRobots()
 * @method string getTitle()
 * @method $this setContentType(string $value)
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
