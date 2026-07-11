<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * Convert php serialize parser
 *
 * @package    Mage_Dataflow
 */
class Mage_Dataflow_Model_Convert_Parser_Serialize extends Mage_Dataflow_Model_Convert_Parser_Abstract
{
    public function parse()
    {
        $this->setData(unserialize($this->getData(), ['allowed_classes' => false]));
        return $this;
    }

    public function unparse()
    {
        $this->setData(serialize($this->getData()));
        return $this;
    }
}
