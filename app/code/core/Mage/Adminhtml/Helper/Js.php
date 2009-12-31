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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml JavaScript helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Js extends Mage_Core_Helper_Js
{
    /**
     * Decode serialized grid data
     * 
     * If string is in next format: 1&2&3&4, 
     * than this method convert it to enumerated array
     *
     * @param   string $encoded
     * @return  array
     */
    public function decodeInput($encoded)
    {
        $_data = array();
        parse_str($encoded, $data);
        foreach($data as $key=>$value) {
            if (empty($value)) {
                $_data[] = $key;
            }
            parse_str(base64_decode($value), $data[$key]);
        }
        $data = !empty($_data) ? $_data : $data;
        return $data;
    }
}
