<?php
/**
 * Customer api V2
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Customer
 */
class Mage_Customer_Model_Customer_Api_V2 extends Mage_Customer_Model_Customer_Api
{
    /**
     * Prepare data to insert/update.
     * Creating array for stdClass Object
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareData($data)
    {
        if (($objectVars = get_object_vars($data)) !== null) {
            return parent::_prepareData($objectVars);
        }
        return [];
    }
}
