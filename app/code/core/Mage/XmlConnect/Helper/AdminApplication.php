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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect device helper for Android
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_AdminApplication extends Mage_Core_Helper_Abstract
{
    /**
     * All store views param for a store switcher
     */
    const ALL_STORE_VIEWS = 'all_store_views';

    /**
     * Store id list
     *
     * @var array
     */
    protected $_storeIdList;

    /**
     * View id list
     *
     * @var array
     */
    protected $_viewIdList;

    /**
     * Get store list ids
     *
     * @throws Mage_Core_Exception
     * @param bool $storeList
     * @return array
     */
    public function getSwitcherList($storeList = false)
    {
        $result = array(null);
        $storeSwitcher = Mage::registry('store_switcher');

        if (null === $storeSwitcher) {
            Mage::throwException($this->__('Store switcher hasn\'t been defined'));
        }

        if (empty($storeSwitcher)) {
            return $result;
        }

        if ($storeList) {
            return array_merge($result, $this->_getStoreIdList($storeSwitcher));
        } else {
            return array_merge($result, $this->_getViewIdList($storeSwitcher));
        }
    }

    /**
     * Get store id list
     *
     * @param array $storeSwitcher
     * @return array
     */
    protected function _getStoreIdList($storeSwitcher)
    {
        if (null === $this->_storeIdList) {
            $this->_storeIdList = array();
            foreach ($storeSwitcher as $params) {
                if (empty($params['store_list'])) {
                    continue;
                }
                $storeIds = array_keys($params['store_list']);
                foreach ($storeIds as $storeId) {
                    $this->_storeIdList[] = $storeId;
                }
            }
            sort($this->_storeIdList);
        }
        return $this->_storeIdList;
    }

    /**
     * Get view id list
     *
     * @param array $storeSwitcher
     * @return array
     */
    protected function _getViewIdList($storeSwitcher)
    {
        if (null === $this->_viewIdList) {
            $this->_viewIdList = array();
            foreach ($storeSwitcher as $params) {
                if (empty($params['store_list'])) {
                    continue;
                }
                foreach ($params['store_list'] as $storeData) {
                    if (empty($storeData['view_list'])) {
                        continue;
                    }
                    $viewIds = array_keys($storeData['view_list']);
                    foreach ($viewIds as $viewId) {
                        $this->_viewIdList[] = $viewId;
                    }
                }
            }
            sort($this->_viewIdList);
        }
        return $this->_viewIdList;
    }
}
