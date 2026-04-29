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
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Tax');
    }

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
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action
     * @return void
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
                $this->_getSession()->unsRuleData();
                $this->_getSession()->addError($this->__('This rule no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getRuleData(true);
        if (!empty($data)) {
            $ruleModel->setData($data);
        }

        $this->_title($ruleModel->getId() ? sprintf('%s', $ruleModel->getCode()) : $this->__('New Rule'));

        Mage::register('tax_rule', $ruleModel);

        $this->_initAction()
            ->_addBreadcrumb(
                $taxRuleId ? $this->__('Edit Rule') : $this->__('New Rule'),
                $taxRuleId ? $this->__('Edit Rule') : $this->__('New Rule'),
            )
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_rule_edit')
                ->setData('action', $this->getUrl('*/tax_rule/save')))
            ->renderLayout();
    }

    /**
     * Save action
     * @return $this|Mage_Core_Controller_Response_Http|void
     */
    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if (!$postData) {
            return $this->getResponse()->setRedirect($this->getUrl('*/tax_rule'));
        }

        $ruleId = (int) $this->getRequest()->getParam('tax_calculation_rule_id');
        $ruleModel = Mage::getModel('tax/calculation_rule')->load($ruleId);
        $ruleModel->setData($postData);
        $ruleModel->setCalculateSubtotal($this->getRequest()->getParam('calculate_subtotal', 0));

        try {
            //Check if the rule already exists
            if (!$this->_isValidRuleRequest($ruleModel)) {
                return $this->_redirectReferer();
            }

            $ruleModel->save();
            $this->_getSession()->addSuccess($this->__('The tax rule has been saved.'));

            if ($this->getRequest()->getParam('back')) {
                return $this->_redirect('*/*/edit', ['rule' => $ruleModel->getId()]);
            }

            return $this->_redirect('*/*/');
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addException($mageCoreException, $mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('An error occurred while saving this tax rule.'));
        }

        $this->_getSession()->setRuleData($postData);
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
            (array) $ruleModel->getTaxRate(),
            (array) $ruleModel->getTaxCustomerClass(),
            (array) $ruleModel->getTaxProductClass(),
        );

        //Remove the current one from the list
        $existingRules = array_diff($existingRules, [$ruleModel->getOrigData('code')]);

        //Verify if a Rule already exists. If not throw an error
        if ($existingRules !== []) {
            $ruleCodes = implode(',', $existingRules);
            $this->_getSession()->addError(
                $this->__('Rules (%s) already exist for the specified Tax Rate, Customer Tax Class and Product Tax Class combinations', $ruleCodes),
            );
            return false;
        }

        return true;
    }

    /**
     * Delete action
     * @return void
     */
    public function deleteAction()
    {
        $ruleId = (int) $this->getRequest()->getParam('rule');
        $ruleModel = Mage::getSingleton('tax/calculation_rule')
            ->load($ruleId);
        if (!$ruleModel->getId()) {
            $this->_getSession()->addError($this->__('This rule no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        try {
            $ruleModel->delete();

            $this->_getSession()->addSuccess($this->__('The tax rule has been deleted.'));
            $this->_redirect('*/*/');

            return;
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addException($mageCoreException, $mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('An error occurred while deleting this tax rule.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Initialize action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/tax/rules')
            ->_addBreadcrumb($this->__('Tax'), $this->__('Tax'))
            ->_addBreadcrumb($this->__('Tax Rules'), $this->__('Tax Rules'))
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
    #[Deprecated(message: 'Use Mage::getSingleton', since: OpenMageVersionInterface::VERSION_20_18_0_0)]
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
    #[Deprecated(message: 'Use Mage::helper', since: OpenMageVersionInterface::VERSION_20_18_0_0)]
    protected function _getHelperModel($className)
    {
        return Mage::helper($className);
    }

    /**
     * Controller pre-dispatch method
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    #[Override]
    public function preDispatch()
    {
        $this->_setForcedFormKeyActions('delete');
        return parent::preDispatch();
    }
}
