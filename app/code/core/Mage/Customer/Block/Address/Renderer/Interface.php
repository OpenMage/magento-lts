<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * Address renderer interface
 *
 * @package    Mage_Customer
 */
interface Mage_Customer_Block_Address_Renderer_Interface
{
    /**
     * Set format type object
     */
    public function setType(Varien_Object $type);

    /**
     * Retrieve format type object
     *
     * @return Varien_Object
     */
    public function getType();

    /**
     * Render address
     *
     * @return mixed
     */
    public function render(Mage_Customer_Model_Address_Abstract $address);
}
