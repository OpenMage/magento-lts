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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml poll answer controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Poll_AnswerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'cms/poll';

    public function editAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('cms/poll');
        $this->_addBreadcrumb(
            Mage::helper('poll')->__('Poll Manager'),
            Mage::helper('poll')->__('Poll Manager'),
            $this->getUrl('*/*/')
        );
        $this->_addBreadcrumb(
            Mage::helper('poll')->__('Edit Poll Answer'),
            Mage::helper('poll')->__('Edit Poll Answer')
        );

        $this->_addContent($this->getLayout()->createBlock('adminhtml/poll_answer_edit'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        //print '@@';
        if ($post = $this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('poll/poll_answer');
                $model->setData($post)
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('poll')->__('The answer has been saved.')
                );
                $this->_redirect(
                    '*/poll/edit',
                    ['id' => $this->getRequest()->getParam('poll_id'), 'tab' => 'answers_section']
                );
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/poll_edit_tab_answers_grid')->toHtml()
        );
    }

    public function jsonSaveAction()
    {
        $response = new Varien_Object();
        $response->setError(0);

        if ($post = $this->getRequest()->getPost()) {
            $data = Zend_Json::decode($post['data']);
            try {
                if (trim($data['answer_title']) == '') {
                    throw new Exception(Mage::helper('poll')->__('Invalid Answer.'));
                }
                $model = Mage::getModel('poll/poll_answer');
                $model->setData($data)
                    ->save();
            } catch (Exception $e) {
                $response->setError(1);
                $response->setMessage($e->getMessage());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    public function jsonDeleteAction()
    {
        $response = new Varien_Object();
        $response->setError(0);

        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('poll/poll_answer');
                $model->setId(Zend_Json::decode($id))
                    ->delete();
            } catch (Exception $e) {
                $response->setError(1);
                $response->setMessage($e->getMessage());
            }
        } else {
            $response->setError(1);
            $response->setMessage(Mage::helper('poll')->__('Unable to find an answer to delete.'));
        }
        $this->getResponse()->setBody($response->toJson());
    }
}
