<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Index
 */

/**
 * @package    Mage_Index
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
