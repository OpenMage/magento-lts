<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Free payment method
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Model_Method_Free extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'free';

    public function isAvailable($quote=null)
    {
        if (is_null($quote)) {
           return false;
        }

        /* @var $quote Mage_Sales_Model_Quote */
        $totals = $quote->getTotals();

        if( !isset($totals['grand_total']) ) {
            return false;
        }
        $grandTotal = $totals['grand_total'];

        if( $grandTotal->getValue() == 0 ) {
            return true;
        }

        return false;
    }
}