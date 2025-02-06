<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 */

/**
 * Convert php serialize parser
 *
 * @category   Mage
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
