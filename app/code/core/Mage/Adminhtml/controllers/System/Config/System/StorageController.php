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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml account controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_System_Config_System_StorageController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return synchronize process status flag
     *
     * @return Mage_Core_Model_File_Storage_Flag
     */
    protected function _getSyncFlag()
    {
        return Mage::getModel('core/file_storage_flag')->loadSelf();
    }

    /**
     * Synchronize action between storages
     *
     * @return void
     */
    public function synchronizeAction()
    {
        if (!isset($_REQUEST['storage'])) {
            return;
        }

        session_write_close();

        $flag = $this->_getSyncFlag();
        if ($flag && $flag->getState() == Mage_Core_Model_File_Storage_Flag::STATE_RUNNING
            && $flag->getLastUpdate()
            && time() <= (strtotime($flag->getLastUpdate()) + Mage_Core_Model_File_Storage_Flag::FLAG_TTL)
        ) {
            return;
        }
        $flag->setState(1)->save();

        $storage = array('type' => (int) $_REQUEST['storage']);
        if (isset($_REQUEST['connection']) && !empty($_REQUEST['connection'])) {
            $storage['connection'] = $_REQUEST['connection'];
        }

        try {
            Mage::getModel('core/file_storage')->synchronize($storage, $flag);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $flag->delete();
    }

    /**
     * Retrieve synchronize process state and it's parameters in json format 
     *
     * @return void
     */
    public function statusAction()
    {
        $flag = $this->_getSyncFlag();
        if ($flag && $flag->getState() == Mage_Core_Model_File_Storage_Flag::STATE_RUNNING
            && $flag->getLastUpdate()
            && time() <= (strtotime($flag->getLastUpdate()) + Mage_Core_Model_File_Storage_Flag::FLAG_TTL)
        ) {
            $result = array('state' => Mage_Core_Model_File_Storage_Flag::STATE_RUNNING);

            $flagData = $flag->getFlagData();
            if (is_array($flagData)
                && isset($flagData['source']) && !empty($flagData['source'])
                && isset($flagData['destination']) && !empty($flagData['destination'])
            ) {
                $result['message'] = Mage::helper('adminhtml')->__('Synchronizing %s to %s', $flagData['source'], $flagData['destination']);
            }
        } else {
            $result = array('state' => Mage_Core_Model_File_Storage_Flag::STATE_INACTIVE);
        }

        $result = Mage::helper('core')->jsonEncode($result);
        Mage::app()->getResponse()->setBody($result);
    }
}
