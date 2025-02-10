<?php
/**
 * Review session model
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Review
 * @method array getFormData()
 * @method $this setFormData(array $value)
 * @method array getRedirectUrl()
 * @method $this setRedirectUrl(string $value)
 */
class Mage_Review_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('review');
    }
}
