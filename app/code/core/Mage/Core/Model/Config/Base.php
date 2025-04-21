<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Abstract configuration class
 *
 * Used to retrieve core configuration values
 *
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
