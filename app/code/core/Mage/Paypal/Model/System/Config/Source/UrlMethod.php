<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Paypal
 */

/**
 * Source model for url method: GET/POST
 *
 * @category   Mage
 * @package    Mage_Paypal
 */
class Mage_Paypal_Model_System_Config_Source_UrlMethod
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'GET', 'label' => 'GET'],
            ['value' => 'POST', 'label' => 'POST'],
        ];
    }
}
