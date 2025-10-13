<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Template db resource
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Email_Template extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('core/email_template', 'template_id');
    }

    /**
     * Load by template code from DB.
     *
     * @param string $templateCode
     * @return array
     */
    public function loadByCode($templateCode)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('template_code = :template_code');
        $result = $this->_getReadAdapter()->fetchRow($select, ['template_code' => $templateCode]);

        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * Check usage of template code in other templates
     *
     * @return bool
     */
    public function checkCodeUsage(Mage_Core_Model_Email_Template $template)
    {
        if ($template->getTemplateActual() != 0 || is_null($template->getTemplateActual())) {
            $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable(), 'COUNT(*)')
                ->where('template_code = :template_code');
            $bind = [
                'template_code' => $template->getTemplateCode(),
            ];

            $templateId = $template->getId();
            if ($templateId) {
                $select->where('template_id != :template_id');
                $bind['template_id'] = $templateId;
            }

            $result = $this->_getReadAdapter()->fetchOne($select, $bind);
            if ($result) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set template type, added at and modified at time
     *
     * @param Mage_Core_Model_Email_Template $object
     * @inheritDoc
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->formatDate(true));
        }

        $object->setModifiedAt($this->formatDate(true));
        $object->setTemplateType((int) $object->getTemplateType());

        return parent::_beforeSave($object);
    }

    /**
     * Retrieve config scope and scope id of specified email template by email paths
     *
     * @param array $paths
     * @param int|string $templateId
     * @return array
     */
    public function getSystemConfigByPathsAndTemplateId($paths, $templateId)
    {
        $orWhere = [];
        $pathesCounter = 1;
        $bind = [];
        foreach ($paths as $path) {
            $pathAlias = 'path_' . $pathesCounter;
            $orWhere[] = 'path = :' . $pathAlias;
            $bind[$pathAlias] = $path;
            $pathesCounter++;
        }

        $bind['template_id'] = $templateId;
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('core/config_data'), ['scope', 'scope_id', 'path'])
            ->where('value LIKE :template_id')
            ->where(implode(' OR ', $orWhere));

        return $this->_getReadAdapter()->fetchAll($select, $bind);
    }
}
