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
 * @package     Mage_PageCache
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page cache admin controller
 *
 * @category    Mage
 * @package     Mage_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PageCache_Adminhtml_PageCacheController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Retrieve session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Clean external cache action
     *
     * @return void
     */
    public function cleanAction()
    {
        try {
            if (Mage::helper('pagecache')->isEnabled()) {
                Mage::helper('pagecache')->getCacheControlInstance()->clean();
                $this->_getSession()->addSuccess(
                    Mage::helper('pagecache')->__('The external full page cache has been cleaned.')
                );
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException(
                $e,
                Mage::helper('pagecache')->__('An error occurred while clearing the external full page cache.')
            );
        }
        $this->_redirect('*/cache/index');
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('page_cache');
    }
}
