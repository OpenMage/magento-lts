<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Tax rule controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Tax_RuleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/tax/rules';

    /**
     * Index action
     *
     * @return $this
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Manage Tax Rules'));

        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule'))
            ->renderLayout();
        return $this;
    }

    /**
     * Redirect to edit action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action
     */
    public function editAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Manage Tax Rules'));

        $taxRuleId  = $this->getRequest()->getParam('rule');
        $ruleModel  = Mage::getModel('tax/calculation_rule');
        if ($taxRuleId) {
            $ruleModel->load($taxRuleId);
            if (!$ruleModel->getId()) {
                Mage::getSingleton('adminhtml/session')->unsRuleData();
                Mage::getSingleton('adminhtml/session')
                    ->addError(Mage::helper('tax')->__('This rule no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = Mage::getSingleton('adminhtml/session')->getRuleData(true);
        if (!empty($data)) {
            $ruleModel->setData($data);
        }

        $this->_title($ruleModel->getId() ? sprintf('%s', $ruleModel->getCode()) : $this->__('New Rule'));

        Mage::register('tax_rule', $ruleModel);

        $this->_initAction()
            ->_addBreadcrumb(
                $taxRuleId ? Mage::helper('tax')->__('Edit Rule') : Mage::helper('tax')->__('New Rule'),
                $taxRuleId ? Mage::helper('tax')->__('Edit Rule') : Mage::helper('tax')->__('New Rule'),
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule_edit')
                ->setData('action', $this->getUrl('*/tax_rule/save')))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if (!$postData) {
            return $this->getResponse()->setRedirect($this->getUrl('*/tax_rule'));
        }

        $ruleId = (int) $this->getRequest()->getParam('tax_calculation_rule_id');
        $ruleModel = $this->_getSingletonModel('tax/calculation_rule')->load($ruleId);
        $ruleModel->setData($postData);
        $ruleModel->setCalculateSubtotal($this->getRequest()->getParam('calculate_subtotal', 0));

        /** @var Mage_Adminhtml_Model_Session $session */
        $session = $this->_getSingletonModel('adminhtml/session');

        try {
            //Check if the rule already exists
            if (!$this->_isValidRuleRequest($ruleModel)) {
                return $this->_redirectReferer();
            }

            $ruleModel->save();
            $session->addSuccess($this->_getHelperModel('tax')->__('The tax rule has been saved.'));

            if ($this->getRequest()->getParam('back')) {
                return $this->_redirect('*/*/edit', ['rule' => $ruleModel->getId()]);
            }

            return $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception) {
            $session->addError($this->_getHelperModel('tax')->__('An error occurred while saving this tax rule.'));
        }

        $this->_getSingletonModel('adminhtml/session')->setRuleData($postData);
        $this->_redirectReferer();
    }

    /**
     * Check if this a duplicate rule creation request
     *
     * @param  Mage_Tax_Model_Calculation_Rule $ruleModel
     * @return bool
     */
    protected function _isValidRuleRequest($ruleModel)
    {
        $existingRules = $ruleModel->fetchRuleCodes(
            $ruleModel->getTaxRate(),
            $ruleModel->getTaxCustomerClass(),
            $ruleModel->getTaxProductClass(),
        );

        /** @var Mage_Adminhtml_Model_Session $session */
        $session = $this->_getSingletonModel('adminhtml/session');

        //Remove the current one from the list
        $existingRules = array_diff($existingRules, [$ruleModel->getOrigData('code')]);

        //Verify if a Rule already exists. If not throw an error
        if ($existingRules !== []) {
            $ruleCodes = implode(',', $existingRules);
            $session->addError(
                $this->_getHelperModel('tax')->__('Rules (%s) already exist for the specified Tax Rate, Customer Tax Class and Product Tax Class combinations', $ruleCodes),
            );
            return false;
        }

        return true;
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $ruleId = (int) $this->getRequest()->getParam('rule');
        $ruleModel = Mage::getSingleton('tax/calculation_rule')
            ->load($ruleId);
        if (!$ruleModel->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('tax')->__('This rule no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $ruleModel->delete();

            Mage::getSingleton('adminhtml/session')
                ->addSuccess(Mage::helper('tax')->__('The tax rule has been deleted.'));
            $this->_redirect('*/*/');

            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('tax')->__('An error occurred while deleting this tax rule.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/tax/rules')
            ->_addBreadcrumb(Mage::helper('tax')->__('Tax'), Mage::helper('tax')->__('Tax'))
            ->_addBreadcrumb(Mage::helper('tax')->__('Tax Rules'), Mage::helper('tax')->__('Tax Rules'))
        ;
        return $this;
    }

    /**
     * Return model instance
     *
     * @param  string                   $className
     * @param  array                    $arguments
     * @return Mage_Core_Model_Abstract
     */
    protected function _getSingletonModel($className, $arguments = [])
    {
        return Mage::getSingleton($className, $arguments);
    }

    /**
     * Return helper instance
     *
     * @param  string                    $className
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelperModel($className)
    {
        return Mage::helper($className);
    }

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete');
        return parent::preDispatch();
    }
}
