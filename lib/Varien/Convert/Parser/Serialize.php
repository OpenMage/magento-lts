<?php

/**
 * @category   Varien
 * @package    Varien_Convert
 */

/**
 * Convert php serialize parser
 *
 * @category   Varien
 * @package    Varien_Convert
 */
class Varien_Convert_Parser_Serialize extends Varien_Convert_Parser_Abstract
{
    public function parse()
    {
        $this->setData(unserialize($this->getData()));
        return $this;
    }

    public function unparse()
    {
        $this->setData(serialize($this->getData()));
        return $this;
    }
}
