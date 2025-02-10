<?php
/**
 * Auth session model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 * @method array|string getProductIds()
 * @method $this setProductIds(array|string $value)
 */
class Mage_Adminhtml_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('adminhtml');
    }
}
