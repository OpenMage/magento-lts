<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Newsletter template resource model
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Resource_Template extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('newsletter/template', 'template_id');
    }

    /**
     * Load an object by template code
     *
     * @param  string $templateCode
     * @return $this
     */
    public function loadByCode(Mage_Newsletter_Model_Template $object, $templateCode)
    {
        $read = $this->_getReadAdapter();
        if ($read && !is_null($templateCode)) {
            $select = $this->_getLoadSelect('template_code', $templateCode, $object)
                ->where('template_actual = :template_actual');
            $data = $read->fetchRow($select, ['template_actual' => 1]);

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
     * @return bool
     */
    public function checkUsageInQueue(Mage_Newsletter_Model_Template $template)
    {
        if ($template->getTemplateActual() !== 0 && !$template->getIsSystem()) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('newsletter/queue'), new Zend_Db_Expr('COUNT(queue_id)'))
                ->where('template_id = :template_id');
            $countOfQueue = $this->_getReadAdapter()->fetchOne($select, ['template_id' => $template->getId()]);
            return $countOfQueue > 0;
        }

        if ($template->getIsSystem()) {
            return false;
        }

        return true;
    }

    /**
     * Check usage of template code in other templates
     *
     * @return bool
     */
    public function checkCodeUsage(Mage_Newsletter_Model_Template $template)
    {
        if ($template->getTemplateActual() != 0 || is_null($template->getTemplateActual())) {
            $bind = [
                'template_id'     => $template->getId(),
                'template_code'   => $template->getTemplateCode(),
                'template_actual' => 1,
            ];
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), new Zend_Db_Expr('COUNT(template_id)'))
                ->where('template_id != :template_id')
                ->where('template_code = :template_code')
                ->where('template_actual = :template_actual');

            $countOfCodes = $this->_getReadAdapter()->fetchOne($select, $bind);

            return $countOfCodes > 0;
        }

        return false;
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Newsletter_Model_Template $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($this->checkCodeUsage($object)) {
            Mage::throwException(Mage::helper('newsletter')->__('Duplicate template code.'));
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
