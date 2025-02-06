<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Auth session model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
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
