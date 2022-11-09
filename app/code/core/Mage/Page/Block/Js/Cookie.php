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
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Js_Cookie extends Mage_Core_Block_Template
{
    /**
     * Get cookie model instance
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::getSingleton('core/cookie');
    }
    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->getCookie()->getDomain();
        if (!empty($domain[0]) && ($domain[0] !== '.')) {
            $domain = '.'.$domain;
        }
        return $domain;
    }

    /**
     * Get configured cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getCookie()->getPath();
    }
}
