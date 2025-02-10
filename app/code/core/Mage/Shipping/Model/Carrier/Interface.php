<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Shipping
 */
interface Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Check if carrier has shipping tracking option available
     *
     * @return bool
     */
    public function isTrackingAvailable();

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods();
}
