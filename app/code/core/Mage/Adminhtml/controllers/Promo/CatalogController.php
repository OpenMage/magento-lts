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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend Catalog Price Rules controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Promo_CatalogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Dirty rules notice message
     *
     * @var string
     */
    protected $_dirtyRulesNoticeMessage;

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('promo/catalog')
            ->_addBreadcrumb(
                Mage::helper('catalogrule')->__('Promotions'),
                Mage::helper('catalogrule')->__('Promotions')
            );
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Catalog Price Rules'));

        $dirtyRules = Mage::getModel('catalogrule/flag')->loadSelf();
        if ($dirtyRules->getState()) {
            Mage::getSingleton('adminhtml/session')->addNotice($this->getDirtyRulesNoticeMessage());
        }

        $this->_initAction()
            ->_addBreadcrumb(
                Mage::helper('catalogrule')->__('Catalog'),
                Mage::helper('catalogrule')->__('Catalog')
            )
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Catalog Price Rules'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalogrule/rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('catalogrule')->__('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        Mage::register('current_promo_catalog_rule', $model);

        $this->_initAction()->getLayout()->getBlock('promo_catalog_edit')
             ->setData('action', $this->getUrl('*/promo_catalog/save'));

        $breadcrumb = $id
            ? Mage::helper('catalogrule')->__('Edit Rule')
            : Mage::helper('catalogrule')->__('New Rule');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb)->renderLayout();

    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('catalogrule/rule');
                Mage::dispatchEvent(
                    'adminhtml_controller_catalogrule_prepare_save',
                    array('request' => $this->getRequest())
                );
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                if ($id = $this->getRequest()->getParam('rule_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('catalogrule')->__('Wrong rule specified.'));
                    }
                }

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);

                $autoApply = false;
                if (!empty($data['auto_apply'])) {
                    $autoApply = true;
                    unset($data['auto_apply']);
                }

                $model->loadPost($data);

                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalogrule')->__('The rule has been saved.')
                );
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                if ($autoApply) {
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    Mage::getModel('catalogrule/flag')->loadSelf()
                        ->setState(1)
                        ->save();
                    if ($this->getRequest()->getParam('back')) {
                        $this->_redirect('*/*/edit', array('id' => $model->getId()));
                        return;
                    }
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.')
                );
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('catalogrule/rule');
                $model->load($id);
                $model->delete();
                Mage::getModel('catalogrule/flag')->loadSelf()
                    ->setState(1)
                    ->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('catalogrule')->__('The rule has been deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while deleting the rule. Please review the log and try again.')
                );
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('catalogrule')->__('Unable to find a rule to delete.')
        );
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function chooserAction()
    {
        switch ($this->getRequest()->getParam('attribute')) {
            case 'sku':
                $type = 'adminhtml/promo_widget_chooser_sku';
                break;

            case 'categories':
                $type = 'adminhtml/promo_widget_chooser_categories';
                break;
        }
        if (!empty($type)) {
            $block = $this->getLayout()->createBlock($type);
            if ($block) {
                $this->getResponse()->setBody($block->toHtml());
            }
        }
    }

    public function newActionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('catalogrule/rule'))
            ->setPrefix('actions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Action_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Apply all active catalog price rules
     */
    public function applyRulesAction()
    {
        $errorMessage = Mage::helper('catalogrule')->__('Unable to apply rules.');
        try {
            Mage::getModel('catalogrule/rule')->applyAll();
            Mage::getModel('catalogrule/flag')->loadSelf()
                ->setState(0)
                ->save();
            $this->_getSession()->addSuccess(Mage::helper('catalogrule')->__('The rules have been applied.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($errorMessage);
            Mage::logException($e);
        }
        $this->_redirect('*/*');
    }

    /**
     * @deprecated since 1.5.0.0
     */
    public function addToAlersAction()
    {
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/catalog');
    }

    /**
     * Set dirty rules notice message
     *
     * @param string $dirtyRulesNoticeMessage
     */
    public function setDirtyRulesNoticeMessage($dirtyRulesNoticeMessage)
    {
        $this->_dirtyRulesNoticeMessage = $dirtyRulesNoticeMessage;
    }

    /**
     * Get dirty rules notice message
     *
     * @return string
     */
    public function getDirtyRulesNoticeMessage()
    {
        $defaultMessage = Mage::helper('catalogrule')->__('There are rules that have been changed but were not applied. Please, click Apply Rules in order to see immediate effect in the catalog.');
        return $this->_dirtyRulesNoticeMessage ? $this->_dirtyRulesNoticeMessage : $defaultMessage;
    }
}
