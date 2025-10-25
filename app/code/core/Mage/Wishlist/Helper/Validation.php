<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Helper_Validation extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Wishlist';

    public function validateEmails(array $emails): ConstraintViolationListInterface
    {
        /** @var Mage_Validation_Helper_Data $validator */
        $validator = Mage::helper('validation');
    }
}
