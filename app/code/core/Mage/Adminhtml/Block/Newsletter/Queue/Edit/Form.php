<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter queue edit form
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form for newsletter queue editing.
     * Form can be run from newsletter template grid by option "Queue newsletter"
     * or from  newsletter queue grid by edit option.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var Mage_Newsletter_Model_Queue $queue */
        $queue = Mage::getSingleton('newsletter/queue');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend'    =>  Mage::helper('newsletter')->__('Queue Information'),
            'class'    =>  'fieldset-wide',
        ]);

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        if ($queue->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            $fieldset->addField('date', 'date', [
                'name'      =>    'start_at',
                'time'      =>    true,
                'format'    =>    $outputFormat,
                'label'     =>    Mage::helper('newsletter')->__('Queue Date Start'),
                'image'     =>    $this->getSkinUrl('images/grid-cal.gif'),
            ]);

            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores', 'multiselect', [
                    'name'          => 'stores[]',
                    'label'         => Mage::helper('newsletter')->__('Subscribers From'),
                    'image'         => $this->getSkinUrl('images/grid-cal.gif'),
                    'values'        => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                    'value'         => $queue->getStores(),
                ]);
            } else {
                $fieldset->addField('stores', 'hidden', [
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId(),
                ]);
            }
        } else {
            $fieldset->addField('date', 'date', [
                'name'      => 'start_at',
                'time'      => true,
                'disabled'  => 'true',
                'style'     => 'width:38%;',
                'format'    => $outputFormat,
                'label'     => Mage::helper('newsletter')->__('Queue Date Start'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            ]);

            if (!Mage::app()->isSingleStoreMode()) {
                $fieldset->addField('stores', 'multiselect', [
                    'name'          => 'stores[]',
                    'label'         => Mage::helper('newsletter')->__('Subscribers From'),
                    'image'         => $this->getSkinUrl('images/grid-cal.gif'),
                    'required'      => true,
                    'values'        => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
                    'value'         => $queue->getStores(),
                ]);
            } else {
                $fieldset->addField('stores', 'hidden', [
                    'name'      => 'stores[]',
                    'value'     => Mage::app()->getStore(true)->getId(),
                ]);
            }
        }

        if ($queue->getQueueStartAt()) {
            $form->getElement('date')->setValue(
                Mage::app()->getLocale()->date($queue->getQueueStartAt(), Varien_Date::DATETIME_INTERNAL_FORMAT),
            );
        }

        $fieldset->addField('subject', 'text', [
            'name'      => 'subject',
            'label'     => Mage::helper('newsletter')->__('Subject'),
            'required'  => true,
            'value'     => (
                $queue->isNew() ? $queue->getTemplate()->getTemplateSubject() : $queue->getNewsletterSubject()
            ),
        ]);

        $fieldset->addField('sender_name', 'text', [
            'name'      => 'sender_name',
            'label'     => Mage::helper('newsletter')->__('Sender Name'),
            'title'     => Mage::helper('newsletter')->__('Sender Name'),
            'required'  => true,
            'value'     => (
                $queue->isNew() ? $queue->getTemplate()->getTemplateSenderName() : $queue->getNewsletterSenderName()
            ),
        ]);

        $fieldset->addField('sender_email', 'text', [
            'name'      => 'sender_email',
            'label'     => Mage::helper('newsletter')->__('Sender Email'),
            'title'     => Mage::helper('newsletter')->__('Sender Email'),
            'class'     => 'validate-email',
            'required'  => true,
            'value'     => (
                $queue->isNew() ? $queue->getTemplate()->getTemplateSenderEmail() : $queue->getNewsletterSenderEmail()
            ),
        ]);

        $widgetFilters = ['is_email_compatible' => 1];
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')
            ->getConfig(['widget_filters' => $widgetFilters]);

        if ($queue->isNew()) {
            $fieldset->addField('text', 'editor', [
                'name'      => 'text',
                'label'     => Mage::helper('newsletter')->__('Message'),
                'state'     => 'html',
                'required'  => true,
                'value'     => $queue->getTemplate()->getTemplateText(),
                'style'     => 'height: 600px;',
                'config'    => $wysiwygConfig,
            ]);

            $fieldset->addField('styles', 'textarea', [
                'name'          => 'styles',
                'label'         => Mage::helper('newsletter')->__('Newsletter Styles'),
                'container_id'  => 'field_newsletter_styles',
                'value'         => $queue->getTemplate()->getTemplateStyles(),
            ]);
        } elseif (Mage_Newsletter_Model_Queue::STATUS_NEVER != $queue->getQueueStatus()) {
            $fieldset->addField('text', 'textarea', [
                'name'      =>    'text',
                'label'     =>    Mage::helper('newsletter')->__('Message'),
                'value'     =>    $queue->getNewsletterText(),
            ]);

            $fieldset->addField('styles', 'textarea', [
                'name'          => 'styles',
                'label'         => Mage::helper('newsletter')->__('Newsletter Styles'),
                'value'         => $queue->getNewsletterStyles(),
            ]);

            $form->getElement('text')->setDisabled('true')->setRequired(false);
            $form->getElement('styles')->setDisabled('true')->setRequired(false);
            $form->getElement('subject')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_name')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_email')->setDisabled('true')->setRequired(false);
            $form->getElement('stores')->setDisabled('true');
        } else {
            $fieldset->addField('text', 'editor', [
                'name'      =>    'text',
                'label'     =>    Mage::helper('newsletter')->__('Message'),
                'state'     => 'html',
                'required'  => true,
                'value'     =>    $queue->getNewsletterText(),
                'style'     => 'height: 600px;',
                'config'    => $wysiwygConfig,
            ]);

            $fieldset->addField('styles', 'textarea', [
                'name'          => 'styles',
                'label'         => Mage::helper('newsletter')->__('Newsletter Styles'),
                'value'         => $queue->getNewsletterStyles(),
                'style'         => 'height: 300px;',
            ]);
        }

        $this->setForm($form);
        return $this;
    }
}
