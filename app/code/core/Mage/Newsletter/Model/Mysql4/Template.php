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
 * @package     Mage_Newsletter
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template db resource
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Mysql4_Template extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('newsletter/template', 'template_id');
    }

    /**
     * Load an object by template code
     *
     * @param Mage_Newsletter_Model_Template $object
     * @param string $templateCode
     * @return Mage_Newsletter_Model_Mysql4_Template
     */
    public function loadByCode(Mage_Newsletter_Model_Template $object, $templateCode)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($templateCode)) {
            $select = $this->_getLoadSelect('template_code', $templateCode, $object)
                ->where('template_actual=?', 1);
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            }
        }

        $this->_afterLoad($object);

        return $this;
    }

    /**
     * Check usage of template in queue
     *
     * @param  Mage_Newsletter_Model_Template $template
     * @return boolean
     */
    public function checkUsageInQueue(Mage_Newsletter_Model_Template $template)
    {
        if ($template->getTemplateActual() !== 0 && !$template->getIsSystem()) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('newsletter/queue'), new Zend_Db_Expr('COUNT(queue_id)'))
                ->where('template_id=?',$template->getId());

            $countOfQueue = $this->_getReadAdapter()->fetchOne($select);

            return $countOfQueue > 0;
        }
        elseif ($template->getIsSystem()) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * Check usage of template code in other templates
     *
     * @param   Mage_Newsletter_Model_Template $template
     * @return  boolean
     */
    public function checkCodeUsage(Mage_Newsletter_Model_Template $template)
    {
        if ($template->getTemplateActual() != 0 || is_null($template->getTemplateActual())) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), new Zend_Db_Expr('COUNT(template_id)'))
                ->where('template_id!=?',$template->getId())
                ->where('template_code=?',$template->getTemplateCode())
                ->where('template_actual=?',1);

            $countOfCodes = $this->_getReadAdapter()->fetchOne($select);

            return $countOfCodes > 0;
        } else {
            return false;
        }
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Newsletter_Model_Mysql4_Template
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($this->checkCodeUsage($object)) {
            Mage::throwException(Mage::helper('newsletter')->__('Duplicate of template code'));
        }

        if (!$object->hasTemplateActual()) {
            $object->setTemplateActual(1);
        }
        if (!$object->hasAddedAt()) {
            $object->setAddedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }
}
