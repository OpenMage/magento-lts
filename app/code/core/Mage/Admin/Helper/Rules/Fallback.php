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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Admin
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin Data Helper
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Admin_Helper_Rules_Fallback extends Mage_Core_Helper_Abstract
{
    /**
     * Fallback to resource parent node
     * @param $resourceId
     *
     * @return string
     */
    protected function _getParentResourceId($resourceId)
    {
        $resourcePathInfo = explode('/', $resourceId);
        array_pop($resourcePathInfo);
        return implode('/', $resourcePathInfo);
    }


    /**
     * Fallback resource permissions similarly to zend_acl
     * @param        $resources
     * @param        $resourceId
     * @param string $defaultValue
     *
     * @return string
     */
    public function fallbackResourcePermissions(
        &$resources,
        $resourceId,
        $defaultValue = Mage_Admin_Model_Rules::RULE_PERMISSION_DENIED
    ) {
        if (empty($resourceId)) {
            return $defaultValue;
        }

        if (!array_key_exists($resourceId, $resources)) {
            return $this->fallbackResourcePermissions($resources, $this->_getParentResourceId($resourceId));
        }

        return $resources[$resourceId];
    }
}
