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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template db resource
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_Email_Template extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Templates table name
     * @var string
     */
    protected $_templateTable;

    /**
     * DB write connection
     */
    protected $_write;

    /**
     * DB read connection
     */
    protected $_read;

    /**
     * Initialize email template resource model
     *
     */
    protected function _construct()
    {
        $this->_init('core/email_template', 'template_id');
        $this->_templateTable = $this->getTable('core/email_template');
        $this->_read = $this->_getReadAdapter();
        $this->_write = $this->_getWriteAdapter();
    }

    /**
     * Load by template code from DB.
     *
     * @param  string $templateCode
     * @return array
     */
    public function loadByCode($templateCode)
    {
        $select = $this->_read->select()
            ->from($this->_templateTable)
            ->where('template_code=?', $templateCode);


        $result = $this->_read->fetchRow($select);

        if (!$result) {
            return array();
        }

        return $result;
    }


    /**
     * Check usage of template code in other templates
     *
     * @param   Mage_Core_Model_Email_Template $template
     * @return  boolean
     */
    public function checkCodeUsage(Mage_Core_Model_Email_Template $template)
    {
        if($template->getTemplateActual()!=0 || is_null($template->getTemplateActual())) {

            $select = $this->_read->select()
                ->from($this->_templateTable, new Zend_Db_Expr('COUNT(template_id)'))
                ->where('template_id!=?',$template->getId())
                ->where('template_code=?',$template->getTemplateCode());

            $countOfCodes = $this->_read->fetchOne($select);

            return $countOfCodes > 0;
        } else {
            return false;
        }
    }

   /**
     * Set template type, added at and modified at time
     *
     * @param Varien_Object $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if(!$object->getAddedAt()) {
            $object->setAddedAt(Mage::getSingleton('core/date')->gmtDate());
            $object->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setTemplateType((int)$object->getTemplateType());
        return parent::_beforeSave($object);
    }

    /**
     * Retrieve config scope and scope id of specified email template by email pathes
     *
     * @param array $paths
     * @param int|string $templateId
     * @return array
     */
    public function getSystemConfigByPathsAndTemplateId($paths, $templateId)
    {
        $adapter = $this->_getReadAdapter();
        $orWhere = array();
        foreach ($paths as $path) {
            $orWhere[] = $adapter->quoteInto('path = ?', $path);
        }
        $select = $this->_read->select()
            ->from($this->getTable('core/config_data'), array('scope', 'scope_id', 'path'))
            ->where('value=?', $templateId)
            ->where(join(' OR ', $orWhere));

        $result = $this->_read->fetchAll($select);
        if (!$result) {
            return array();
        }
        return $result;
    }
}
