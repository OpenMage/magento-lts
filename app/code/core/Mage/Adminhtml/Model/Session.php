<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Auth session model
 *
 * @package    Mage_Adminhtml
 *
 * @method array|string getProductIds()
 * @method $this        setProductIds(array|string $value)
 */
class Mage_Adminhtml_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('adminhtml');
    }
}
