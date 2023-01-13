<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Report Flag Model
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Flag extends Mage_Core_Model_Flag
{
    public const REPORT_ORDER_FLAG_CODE    = 'report_order_aggregated';
    public const REPORT_TAX_FLAG_CODE      = 'report_tax_aggregated';
    public const REPORT_SHIPPING_FLAG_CODE = 'report_shipping_aggregated';
    public const REPORT_INVOICE_FLAG_CODE  = 'report_invoiced_aggregated';
    public const REPORT_REFUNDED_FLAG_CODE = 'report_refunded_aggregated';
    public const REPORT_COUPONS_FLAG_CODE  = 'report_coupons_aggregated';
    public const REPORT_BESTSELLERS_FLAG_CODE = 'report_bestsellers_aggregated';
    public const REPORT_PRODUCT_VIEWED_FLAG_CODE = 'report_product_viewed_aggregated';

    /**
     * Setter for flag code
     *
     * @param string $code
     * @return $this
     */
    public function setReportFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
