<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 * @category   Mage
 * @package    Mage_Customer
 */

/**
 * Address renderer interface
 *
 * @category   Mage
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
