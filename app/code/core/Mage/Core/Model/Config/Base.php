<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Abstract configuration class
 *
 * Used to retrieve core configuration values
 *
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Model_Config_Base extends Varien_Simplexml_Config
{
    /**
     * @param string|null $sourceData
     */
    public function __construct($sourceData = null)
    {
        $this->_elementClass = 'Mage_Core_Model_Config_Element';
        parent::__construct($sourceData);
    }
}
