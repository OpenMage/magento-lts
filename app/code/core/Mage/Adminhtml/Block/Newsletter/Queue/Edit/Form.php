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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter queue edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $queue = Mage::getSingleton('newsletter/queue');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    =>  Mage::helper('newsletter')->__('Queue Information')
        ));

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        if($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            $fieldset->addField('date', 'date',array(
                'name'      =>    'start_at',
                'time'      =>    true,
                'format'    =>    $outputFormat,
                'label'     =>    Mage::helper('newsletter')->__('Queue Date Start'),
                'image'     =>    $this->getSkinUrl('images/grid-cal.gif')
            ));

            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores','multiselect',array(
                    'name'          => 'stores[]',
                    'label'         => Mage::helper('newsletter')->__('Subscribers From'),
                    'image'         => $this->getSkinUrl('images/grid-cal.gif'),
                    'values'        => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                    'value'         => $queue->getStores()
                ));
            }
            else {
                $fieldset->addField('stores', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                ));
            }
        } else {
            $fieldset->addField('date','date',array(
                'name'      => 'start_at',
                'time'      => true,
                'disabled'  => 'true',
                'format'    => $outputFormat,
                'label'     => Mage::helper('newsletter')->__('Queue Date Start'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif')
            ));

            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores','multiselect',array(
                    'name'          => 'stores[]',
                    'label'         => Mage::helper('newsletter')->__('Subscribers From'),
                    'image'         => $this->getSkinUrl('images/grid-cal.gif'),
                    'required'      => true,
                    'values'        => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                    'value'         => $queue->getStores()
                ));
            }
            else {
                $fieldset->addField('stores', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                ));
            }
        }

        if ($queue->getQueueStartAt()) {
            $form->getElement('date')->setValue(
                Mage::app()->getLocale()->date($queue->getQueueStartAt(), Varien_Date::DATETIME_INTERNAL_FORMAT)
            );
        }

        $fieldset->addField('subject', 'text', array(
            'name'      =>'subject',
            'label'     => Mage::helper('newsletter')->__('Subject'),
            'required'  => true,
            'value'     => $queue->getTemplate()->getTemplateSubject()
        ));

        $fieldset->addField('sender_name', 'text', array(
            'name'      =>'sender_name',
            'label'     => Mage::helper('newsletter')->__('Sender Name'),
            'title'     => Mage::helper('newsletter')->__('Sender Name'),
            'required'  => true,
            'value'     => $queue->getTemplate()->getTemplateSenderName()
        ));

        $fieldset->addField('sender_email', 'text', array(
            'name'      =>'sender_email',
            'label'     => Mage::helper('newsletter')->__('Sender Email'),
            'title'     => Mage::helper('newsletter')->__('Sender Email'),
            'class'     => 'validate-email',
            'required'  => true,
            'value'     => $queue->getTemplate()->getTemplateSenderEmail()
        ));

        if (in_array($queue->getQueueStatus(), array(
                Mage_Newsletter_Model_Queue::STATUS_NEVER,
                Mage_Newsletter_Model_Queue::STATUS_PAUSE))) {

            $fieldset->addField('text','editor', array(
                'name'      => 'text',
                'wysiwyg'   => false,
                'label'     => Mage::helper('newsletter')->__('Message'),
                'state'     => 'html',
                'theme'     => 'advanced',
                'required'  => true,
                'value'     => $queue->getTemplate()->getTemplateTextPreprocessed(),
                'style'     => 'width:98%; height: 600px;',
            ));
        } else {
            $fieldset->addField('text','text', array(
                'name'      =>    'text',
                'label'     =>    Mage::helper('newsletter')->__('Message'),
                'value'     =>    $this->getUrl('*/newsletter_template/preview',  array(
                                     'id' => $queue->getTemplate()->getId()
                                  ))
            ));

            $form->getElement('text')->setRenderer(Mage::getModel('adminhtml/newsletter_renderer_text'));
            $form->getElement('subject')->setDisabled('true');
            $form->getElement('sender_name')->setDisabled('true');
            $form->getElement('sender_email')->setDisabled('true');
            $form->getElement('stores')->setDisabled('true');
        }

    /*
        $form->getElement('template')->setRenderer(
            $this->getLayout()->createBlock('adminhtml/newsletter_queue_edit_form_renderer_template')
        );
        */


        $this->setForm($form);
        return parent::_prepareForm();
    }
}// Class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form END
