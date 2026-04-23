<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Recurring profiles view/management controller
 *
 * @package    Mage_Adminhtml
 *
 * @TODO: implement ACL restrictions
 */
class Mage_Adminhtml_Sales_Recurring_ProfileController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/recurring_profile';

    /**
     * Recurring profiles list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('sales')->__('Sales'))->_title(Mage::helper('sales')->__('Recurring Profiles'))
            ->loadLayout()
            ->_setActiveMenu('sales/recurring_profile')
            ->renderLayout();
        return $this;
    }

    /**
     * View recurring profile detales
     * @return void
     */
    public function viewAction()
    {
        try {
            $this->_title(Mage::helper('sales')->__('Sales'))->_title(Mage::helper('sales')->__('Recurring Profiles'));
            $profile = $this->_initProfile();
            $this->loadLayout()
                ->_setActiveMenu('sales/recurring_profile')
                ->_title(Mage::helper('sales')->__('Profile #%s', $profile->getReferenceId()))
                ->renderLayout()
            ;
            return;
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            Mage::logException($exception);
        }

        $this->_redirect('*/*/');
    }

    /**
     * Profiles ajax grid
     * @return void
     */
    public function gridAction()
    {
        try {
            $this->loadLayout()->renderLayout();
            return;
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            Mage::logException($exception);
        }

        $this->_redirect('*/*/');
    }

    /**
     * Profile orders ajax grid
     * @return void
     */
    public function ordersAction()
    {
        try {
            $this->_initProfile();
            $this->loadLayout()->renderLayout();
        } catch (Exception $exception) {
            Mage::logException($exception);
            $this->norouteAction();
        }
    }

    /**
     * Profile state updater action
     * @return void
     */
    public function updateStateAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();

            switch ($this->getRequest()->getParam('action')) {
                case 'cancel':
                    $profile->cancel();
                    break;
                case 'suspend':
                    $profile->suspend();
                    break;
                case 'activate':
                    $profile->activate();
                    break;
            }

            $this->_getSession()->addSuccess(Mage::helper('sales')->__('The profile state has been updated.'));
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addError(Mage::helper('sales')->__('Failed to update the profile.'));
            Mage::logException($exception);
        }

        if ($profile) {
            $this->_redirect('*/*/view', ['profile' => $profile->getId()]);
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Profile information updater action
     * @return void
     */
    public function updateProfileAction()
    {
        $profile = null;
        try {
            $profile = $this->_initProfile();
            $profile->fetchUpdate();
            if ($profile->hasDataChanges()) {
                $profile->save();
                $this->_getSession()->addSuccess($this->__('The profile has been updated.'));
            } else {
                $this->_getSession()->addNotice($this->__('The profile has no changes.'));
            }
        } catch (Mage_Core_Exception $mageCoreException) {
            $this->_getSession()->addError($mageCoreException->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addError($this->__('Failed to update the profile.'));
            Mage::logException($exception);
        }

        if ($profile) {
            $this->_redirect('*/*/view', ['profile' => $profile->getId()]);
        } else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Cutomer billing agreements ajax action
     * @return void
     */
    public function customerGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout(false)
            ->renderLayout();
    }

    /**
     * Initialize customer by ID specified in request
     *
     * @return $this
     */
    protected function _initCustomer()
    {
        $customerId = (int) $this->getRequest()->getParam('id');
        $customer = Mage::getModel('customer/customer');

        if ($customerId) {
            $customer->load($customerId);
        }

        Mage::register('current_customer', $customer);
        return $this;
    }

    /**
     * Load/set profile
     *
     * @return Mage_Sales_Model_Recurring_Profile
     */
    protected function _initProfile()
    {
        $profile = Mage::getModel('sales/recurring_profile')->load($this->getRequest()->getParam('profile'));
        if (!$profile->getId()) {
            Mage::throwException($this->__('Specified profile does not exist.'));
        }

        Mage::register('current_recurring_profile', $profile);
        return $profile;
    }
}
