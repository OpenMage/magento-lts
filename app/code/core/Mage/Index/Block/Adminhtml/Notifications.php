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
 * @package    Mage_Index
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Index
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Index_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template
{
    /**
     * Get array of index names which require data reindex
     *
     * @return array
     */
    public function getProcessesForReindex()
    {
        $res = [];
        $processes = Mage::getSingleton('index/indexer')->getProcessesCollection()->addEventsStats();
        /** @var Mage_Index_Model_Process $process */
        foreach ($processes as $process) {
            if (($process->getStatus() == Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX
                || $process->getEvents() > 0) && $process->getIndexer()->isVisible()
            ) {
                $res[] = $process->getIndexer()->getName();
            }
        }
        return $res;
    }

    /**
     * Get index management url
     *
     * @return string
     */
    public function getManageUrl()
    {
        return $this->getUrl('adminhtml/process/list');
    }

    /**
     * ACL validation before html generation
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::getSingleton('admin/session')->isAllowed('system/index')) {
            return parent::_toHtml();
        }
        return '';
    }
}
