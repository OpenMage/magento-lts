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
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template db resource
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Mysql4_Template
{

    /**
     * Templates table name
     * @var string
     */
    protected $_templateTable;

    /**
     * Queue table name
     * @var string
     */
    protected $_queueTable;

    /**
     * DB write connection
     */
    protected $_write;

    /**
     * DB read connection
     */
    protected $_read;

    /**
     * Constructor
     *
     * Initializes resource
     */
    public function __construct()
    {
        $this->_templateTable = Mage::getSingleton('core/resource')->getTableName('newsletter/template');
        $this->_queueTable = Mage::getSingleton('core/resource')->getTableName('newsletter/queue');
        $this->_read = Mage::getSingleton('core/resource')->getConnection('newsletter_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('newsletter_write');
    }

    /**
     * Load template from DB
     *
     * @param  int $templateId
     * @return array
     */
    public function load($templateId)
    {
        $select = $this->_read->select()
            ->from($this->_templateTable)
            ->where('template_id=?', $templateId);

        $result = $this->_read->fetchRow($select);

        if (!$result) {
            return array();
        }

        return $result;
    }

    /**
     * Load by template code from DB.
     *
     * If $useSystem eq true, loading of system template
     *
     * @param  int $templateId
     * @param  boolean $useSystem
     * @return array
     */
    public function loadByCode($templateCode)
    {
        $select = $this->_read->select()
            ->from($this->_templateTable)
            ->where('template_code=?', $templateCode)
            ->where('template_actual=?',1);


        $result = $this->_read->fetchRow($select);

        if (!$result) {
            return array();
        }

        return $result;
    }

    /**
     * Check usage of template in queue
     *
     * @param  Mage_Newsletter_Model_Template $template
     * @return boolean
     */
    public function checkUsageInQueue(Mage_Newsletter_Model_Template $template)
    {
        if ($template->getTemplateActual()!==0 && !$template->getIsSystem()) {
            $select = $this->_read->select()
                ->from($this->_queueTable, new Zend_Db_Expr('COUNT(queue_id)'))
                ->where('template_id=?',$template->getId());

            $countOfQueue = $this->_read->fetchOne($select);

            return $countOfQueue > 0;
        } else if ($template->getIsSystem()) {
        	return false;
        } else {
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
        if($template->getTemplateActual()!=0 || is_null($template->getTemplateActual())) {

            $select = $this->_read->select()
                ->from($this->_templateTable, new Zend_Db_Expr('COUNT(template_id)'))
                ->where('template_id!=?',$template->getId())
                ->where('template_code=?',$template->getTemplateCode())
                ->where('template_actual=?',1);

            $countOfCodes = $this->_read->fetchOne($select);

            return $countOfCodes > 0;
        } else {
            return false;
        }
    }

    /**
     * Save template to DB
     *
     * @param   Mage_Newsletter_Model_Template $template
     */
    public function save(Mage_Newsletter_Model_Template $template)
    {
        $this->_write->beginTransaction();
        try {
            $data = $this->_prepareSave($template);
            if($template->getId() && (($template->getTemplateActual()==0 && !is_null($template->getTemplateActual())) || !$this->checkUsageInQueue($template))) {
                $this->_write->update($this->_templateTable, $data,
                                      $this->_write->quoteInto('template_id=?',$template->getId()));
            } else if ($template->getId()) {
                // Duplicate entry if template used in queue
                $updata = array();
                $updata['template_actual'] = 0;
                $this->_write->update($this->_templateTable, $updata,
                                      $this->_write->quoteInto('template_id=?',$template->getId()));

                $data['template_text_preprocessed'] = null;
                $this->_write->insert($this->_templateTable, $data);
                $template->setId($this->_write->lastInsertId($this->_templateTable));
            } else {
                $this->_write->insert($this->_templateTable, $data);
                $template->setId($this->_write->lastInsertId($this->_templateTable));
            }

            $this->_write->commit();
        }
        catch (Exception $e) {
            $this->_write->rollBack();
            //echo $e->getMessage();
            throw $e;
        }
    }

    /**
     * Prepares template for saving, validates input data
     *
     * @param   Mage_Newsletter_Model_Template $template
     * @return  array
     */
    protected function _prepareSave(Mage_Newsletter_Model_Template $template)
    {
        $data = array();
        $data['template_code'] = $template->getTemplateCode();
        $data['template_text'] = $template->getTemplateText();
        $data['template_text_preprocessed'] = $template->getTemplateTextPreprocessed();
        $data['template_type'] = (int) $template->getTemplateType();
        $data['template_subject'] = $template->getTemplateSubject();
        $data['template_sender_name'] = $template->getTemplateSenderName();
        $data['template_sender_email'] = $template->getTemplateSenderEmail();
        $data['template_actual'] = ( !is_null($template->getTemplateActual()) && $template->getTemplateActual() == 0  ? 0 : 1 );

        if(!$template->getAddedAt()) {
        	$template->setAddedAt(Mage::getSingleton('core/date')->gmtDate());
        	$template->setModifiedAt(Mage::getSingleton('core/date')->gmtDate());
        }

        $data['modified_at']	 = $template->getModifiedAt();
        $data['added_at']	 = $template->getAddedAt();

        if($this->checkCodeUsage($template)) {
            Mage::throwException(Mage::helper('newsletter')->__('Duplicate of template code'));
        }

        $validators = array(
            'template_code' => array(Zend_Filter_Input::ALLOW_EMPTY => false),
            'template_type' => 'Alnum',
            'template_sender_email' => 'EmailAddress',
            'template_sender_name'	=> array(Zend_Filter_Input::ALLOW_EMPTY => false)
        );

        $validateInput = new Zend_Filter_Input(array(), $validators, $data);
        if(!$validateInput->isValid()) {
            $errorString = '';

            foreach($validateInput->getMessages() as $message) {
            	if(is_array($message)) {
                	foreach($message as $str) {
                		$errorString.= $str . "\n";
                	}
            	} else {
            		$errorString.= $message . "\n";
            	}

            }

            Mage::throwException($errorString);
        }

        return $data;
    }

    /**
     * Delete template record in DB.
     *
     * @param   int $templateId
     */
    public function delete($templateId)
    {
        $this->_write->beginTransaction();
        try {
            $this->_write->delete($this->_templateTable, $this->_write->quoteInto('template_id=?', $templateId));
            $this->_write->commit();
        }
        catch(Exception $e) {
            $this->_write->rollBack();
            Mage::throwException(Mage::helper('newsletter')->__('Cannot delete template'));
        }
    }
}
