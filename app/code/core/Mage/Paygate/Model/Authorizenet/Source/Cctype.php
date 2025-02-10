<?php
/**
 * Authorizenet Payment CC Types Source Model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Paygate
 */
class Mage_Paygate_Model_Authorizenet_Source_Cctype extends Mage_Payment_Model_Source_Cctype
{
    public function getAllowedTypes()
    {
        return ['VI', 'MC', 'AE', 'DI', 'OT'];
    }
}
