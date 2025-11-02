<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Review session model
 *
 * @package    Mage_Review
 *
 * @method array getFormData()
 * @method array getRedirectUrl()
 * @method $this setFormData(array $value)
 * @method $this setRedirectUrl(string $value)
 */
class Mage_Review_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('review');
    }
}
