<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Review
 */

/**
 * Review session model
 *
 * @category   Mage
 * @package    Mage_Review
 *
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
