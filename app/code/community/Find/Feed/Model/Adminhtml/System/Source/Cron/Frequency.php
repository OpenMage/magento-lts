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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    
 * @package     _storage
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Source for cron frequency 
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Model_Adminhtml_System_Source_Cron_Frequency
{
    const DAILY   = 1;
    const WEEKLY  = 2;
    const MONTHLY = 3;
    const EVERY_MINUTE = 4;

    /**
     * Fetch options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
               'label' => 'Daily',
               'value' => self::DAILY),
            array(
               'label' => 'Weekly',
               'value' => self::WEEKLY),
            array(
                'label' => 'Monthly',
                'value' => self::MONTHLY),
            array(
                'label' => 'Every minute',
                'value' => self::EVERY_MINUTE)
        );
    }
}
