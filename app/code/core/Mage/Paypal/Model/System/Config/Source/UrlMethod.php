<?php
/**
 * Source model for url method: GET/POST
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
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
