<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Index admin controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Render specified template
     *
     * @param string $tplName
     * @param array $data parameters required by template
     */
    protected function _outTemplate($tplName, $data = [])
    {
        $this->_initLayoutMessages('adminhtml/session');
        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate("$tplName.phtml");
        foreach ($data as $index => $value) {
            $block->assign($index, $value);
        }

        $html = $block->toHtml();
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        $this->getResponse()->setBody($html);
    }

    /**
     * Admin area entry point
     * Always redirects to the startup page url
     */
    public function indexAction()
    {
        $session = Mage::getSingleton('admin/session');
        $url = $session->getUser()->getStartupPageUrl();
        if ($session->isFirstPageAfterLogin()) {
            // retain the "first page after login" value in session (before redirect)
            $session->setIsFirstPageAfterLogin(true);
        }

        $this->_redirect($url);
    }

    /**
     * Administrator login action
     */
    public function loginAction()
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $this->_redirect('*');
            return;
        }

        $loginData = $this->getRequest()->getParam('login');
        $username = (is_array($loginData) && array_key_exists('username', $loginData)) ? $loginData['username'] : null;

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Administrator logout action
     */
    public function logoutAction()
    {
        /** @var Mage_Admin_Model_Session $adminSession */
        $adminSession = Mage::getSingleton('admin/session');
        $adminSession->unsetAll();
        $adminSession->getCookie()->delete($adminSession->getSessionName());
        $adminSession->addSuccess(Mage::helper('adminhtml')->__('You have logged out.'));

        $this->_redirect('*');
    }

    /**
     * Global Search Action
     */
    public function globalSearchAction()
    {
        $searchModules = Mage::getConfig()->getNode('adminhtml/global_search');
        $items = [];

        if (!Mage::getStoreConfigFlag('admin/global_search/enable') || !Mage::getSingleton('admin/session')->isAllowed('admin/global_search')) {
            $items[] = [
                'id' => 'error',
                'type' => Mage::helper('adminhtml')->__('Error'),
                'name' => Mage::helper('adminhtml')->__('Access Denied'),
                'description' => Mage::helper('adminhtml')->__('You have not enough permissions to use this functionality.'),
            ];
            $totalCount = 1;
        } elseif (empty($searchModules)) {
            $items[] = [
                'id' => 'error',
                'type' => Mage::helper('adminhtml')->__('Error'),
                'name' => Mage::helper('adminhtml')->__('No search modules were registered'),
                'description' => Mage::helper('adminhtml')->__('Please make sure that all global admin search modules are installed and activated.'),
            ];
            $totalCount = 1;
        } else {
            $start = $this->getRequest()->getParam('start', 1);
            $limit = $this->getRequest()->getParam('limit', 10);
            $query = $this->getRequest()->getParam('query', '');
            foreach ($searchModules->children() as $searchConfig) {
                if ($searchConfig->acl && !Mage::getSingleton('admin/session')->isAllowed($searchConfig->acl)) {
                    continue;
                }

                $className = $searchConfig->getClassName();

                if (empty($className)) {
                    continue;
                }

                $searchInstance = new $className();
                $results = $searchInstance->setStart($start)
                    ->setLimit($limit)
                    ->setQuery($query)
                    ->load()
                    ->getResults();
                $items = array_merge_recursive($items, $results);
            }

            $totalCount = count($items);
        }

        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('system/autocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    /**
     * Example action
     */
    public function exampleAction()
    {
        $this->_outTemplate('example');
    }

    /**
     * Test action
     */
    public function testAction()
    {
        echo $this->getLayout()->createBlock('core/profiler')->toHtml();
    }

    /**
     * Change locale action
     */
    public function changeLocaleAction()
    {
        $locale = $this->getRequest()->getParam('locale');
        if ($locale) {
            Mage::getSingleton('adminhtml/session')->setLocale($locale);
        }

        $this->_redirectReferer();
    }

    /**
     * Denied JSON action
     */
    public function deniedJsonAction()
    {
        $this->getResponse()->setBody($this->_getDeniedJson());
    }

    /**
     * Retrieve response for deniedJsonAction()
     */
    protected function _getDeniedJson()
    {
        return Mage::helper('core')->jsonEncode([
            'ajaxExpired' => 1,
            'ajaxRedirect' => $this->getUrl('*/index/login'),
        ]);
    }

    /**
     * Denied IFrame action
     */
    public function deniedIframeAction()
    {
        $this->getResponse()->setBody($this->_getDeniedIframe());
    }

    /**
     * Retrieve response for deniedIframeAction()
     */
    protected function _getDeniedIframe()
    {
        return '<script type="text/javascript">parent.window.location = \''
            . $this->getUrl('*/index/login') . '\';</script>';
    }

    /**
     * Forgot administrator password action
     */
    public function forgotpasswordAction()
    {
        $params = $this->getRequest()->getParams();

        if (!(empty($params))) {
            $email = (string) $this->getRequest()->getParam('email');

            if ($this->_validateFormKey()) {
                if (!empty($email)) {
                    // Validate received data to be an email address
                    if (Zend_Validate::is($email, 'EmailAddress')) {
                        $collection = Mage::getResourceModel('admin/user_collection');
                        /** @var Mage_Admin_Model_Resource_User_Collection $collection */
                        $collection->addFieldToFilter('email', $email);
                        $collection->load(false);

                        if ($collection->getSize() > 0) {
                            foreach ($collection as $item) {
                                /** @var Mage_Admin_Model_User $user */
                                $user = Mage::getModel('admin/user')->load($item->getId());
                                if ($user->getId()) {
                                    $newResetPasswordLinkToken = Mage::helper('admin')->generateResetPasswordLinkToken();
                                    $user->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                                    $user->save();
                                    $user->sendPasswordResetConfirmationEmail();
                                }

                                break;
                            }
                        }

                        $this->_getSession()
                            ->addSuccess(
                                $this->__(
                                    'If there is an account associated with %s you will receive an email with a link to reset your password.',
                                    Mage::helper('adminhtml')->escapeHtml($email),
                                ),
                            );
                        $this->_redirect('*/*/login');
                        return;
                    } else {
                        $this->_getSession()->addError($this->__('Invalid email address.'));
                    }
                } else {
                    $this->_getSession()->addError($this->__('The email address is empty.'));
                }
            } else {
                $this->_getSession()->addError($this->__('Invalid Form Key. Please refresh the page.'));
            }
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display reset forgotten password form
     *
     * User is redirected on this action when he clicks on the corresponding link in password reset confirmation email
     */
    public function resetPasswordAction()
    {
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $userId = (int) $this->getRequest()->getQuery('id');
        try {
            $this->_validateResetPasswordLinkToken($userId, $resetPasswordLinkToken);
            $data = [
                'userId' => $userId,
                'resetPasswordLinkToken' => $resetPasswordLinkToken,
                'minAdminPasswordLength' => $this->_getModel('admin/user')->getMinAdminPasswordLength(),
            ];
            $this->_outTemplate('resetforgottenpassword', $data);
        } catch (Exception) {
            $this->_getSession()->addError(Mage::helper('adminhtml')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/forgotpassword', ['_nosecret' => true]);
        }
    }

    /**
     * Reset forgotten password
     *
     * Used to handle data received from reset forgotten password form
     */
    public function resetPasswordPostAction()
    {
        $resetPasswordLinkToken = (string) $this->getRequest()->getQuery('token');
        $userId = (int) $this->getRequest()->getQuery('id');
        $password = (string) $this->getRequest()->getPost('password');
        $passwordConfirmation = (string) $this->getRequest()->getPost('confirmation');

        try {
            $this->_validateResetPasswordLinkToken($userId, $resetPasswordLinkToken);
        } catch (Exception $exception) {
            $this->_getSession()->addError(Mage::helper('adminhtml')->__('Your password reset link has expired.'));
            $this->_redirect('*/*/');
            return;
        }

        if (!$this->_validateFormKey()) {
            $this->_getSession()->addError(Mage::helper('adminhtml')->__('Invalid Form Key. Please refresh the page.'));
            $this->_redirect('*/*/');
            return;
        }

        $errorMessages = [];
        if (iconv_strlen($password) <= 0) {
            $errorMessages[] = Mage::helper('adminhtml')->__('New password field cannot be empty.');
        }

        /** @var Mage_Admin_Model_User $user */
        $user = $this->_getModel('admin/user')->load($userId);

        $user->setNewPassword($password);
        $user->setPasswordConfirmation($passwordConfirmation);

        $validationErrorMessages = $user->validate();
        if (is_array($validationErrorMessages)) {
            $errorMessages = array_merge($errorMessages, $validationErrorMessages);
        }

        if (!empty($errorMessages)) {
            foreach ($errorMessages as $errorMessage) {
                $this->_getSession()->addError($errorMessage);
            }

            $data = [
                'userId' => $userId,
                'resetPasswordLinkToken' => $resetPasswordLinkToken,
                'minAdminPasswordLength' => $this->_getModel('admin/user')->getMinAdminPasswordLength(),
            ];
            $this->_outTemplate('resetforgottenpassword', $data);
            return;
        }

        try {
            // Empty current reset password token i.e. invalidate it
            $user->setRpToken(null);
            $user->setRpTokenCreatedAt(null);
            $user->save();
            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Your password has been updated.'));
            $this->_redirect('*/*/login');
        } catch (Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
            $data = [
                'userId' => $userId,
                'resetPasswordLinkToken' => $resetPasswordLinkToken,
                'minAdminPasswordLength' => $this->_getModel('admin/user')->getMinAdminPasswordLength(),
            ];
            $this->_outTemplate('resetforgottenpassword', $data);
            return;
        }
    }

    /**
     * Check if password reset token is valid
     *
     * @param int $userId
     * @param string $resetPasswordLinkToken
     * @throws Mage_Core_Exception
     */
    protected function _validateResetPasswordLinkToken($userId, $resetPasswordLinkToken)
    {
        if (!is_int($userId)
            || !is_string($resetPasswordLinkToken)
            || empty($resetPasswordLinkToken)
            || empty($userId)
            || $userId < 0
        ) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Invalid password reset token.'));
        }

        /** @var Mage_Admin_Model_User $user */
        $user = Mage::getModel('admin/user')->load($userId);
        if (!$user || !$user->getId()) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Wrong account specified.'));
        }

        $userToken = $user->getRpToken();
        if ($user->isResetPasswordLinkTokenExpired() || !hash_equals($userToken, $resetPasswordLinkToken)) {
            throw Mage::exception('Mage_Core', Mage::helper('adminhtml')->__('Your password reset link has expired.'));
        }
    }

    /**
     * Check if user has permissions to access this controller
     *
     * @return true
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Retrieve model object
     *
     * @link    Mage_Core_Model_Config::getModelInstance
     * @param   string $modelClass
     * @param   array|object $arguments
     * @return  Mage_Core_Model_Abstract|false
     */
    protected function _getModel($modelClass = '', $arguments = [])
    {
        return Mage::getModel($modelClass, $arguments);
    }
}
