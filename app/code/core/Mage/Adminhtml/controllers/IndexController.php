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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _outTemplate($tplName, $data=array())
    {
        $this->_initLayoutMessages('adminhtml/session');
        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate("$tplName.phtml");
        foreach ($data as $index=>$value) {
            $block->assign($index, $value);
        }
        $this->getResponse()->setBody($block->toHtml());
    }

    public function indexAction()
    {
        $url = Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl();

        $this->_redirect($url);
        return;

        $this->loadLayout();
        $block = $this->getLayout()->createBlock('adminhtml/template', 'system.info')
            ->setTemplate('system/info.phtml');

        $this->_addContent($block);

        $this->renderLayout();
    }

    public function loginAction()
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $this->_redirect('*');
            return;
        }
        $loginData = $this->getRequest()->getParam('login');
        $data = array();

        if( is_array($loginData) && array_key_exists('username', $loginData) ) {
            $data['username'] = $loginData['username'];
        } else {
            $data['username'] = null;
        }
        #print_r($data);
        $this->_outTemplate('login', $data);
    }

    public function logoutAction()
    {
        $auth = Mage::getSingleton('admin/session')->unsetAll();
        Mage::getSingleton('adminhtml/session')->unsetAll();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('You successfully logged out.'));
        $this->_redirect('*');
    }

    public function globalSearchAction()
    {
        $searchModules = Mage::getConfig()->getNode("adminhtml/global_search");
        $items = array();

        if ( !Mage::getSingleton('admin/session')->isAllowed('admin/global_search') ) {
            $items[] = array(
                'id'=>'error',
                'type'=>'Error',
                'name'=>Mage::helper('adminhtml')->__('Access Deny'),
                'description'=>Mage::helper('adminhtml')->__('You have not enought permissions to use this functionality.')
            );
            $totalCount = 1;
        } else {
            if (empty($searchModules)) {
                $items[] = array('id'=>'error', 'type'=>'Error', 'name'=>Mage::helper('adminhtml')->__('No search modules registered'), 'description'=>Mage::helper('adminhtml')->__('Please make sure that all global admin search modules are installed and activated.'));
                $totalCount = 1;
            } else {
                $start = $this->getRequest()->getParam('start', 1);
                $limit = $this->getRequest()->getParam('limit', 10);
                $query = $this->getRequest()->getParam('query', '');
                foreach ($searchModules->children() as $searchConfig) {
                    $className = $searchConfig->getClassName();
                    if (empty($className)) {
                        continue;
                    }
                    $searchInstance = new $className();
                    $results = $searchInstance->setStart($start)->setLimit($limit)->setQuery($query)->load()->getResults();
                    $items = array_merge_recursive($items, $results);
                }
                $totalCount = sizeof($items);
            }
        }

        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('system/autocomplete.phtml')
            ->assign('items', $items);

        $this->getResponse()->setBody($block->toHtml());
    }

    public function exampleAction()
    {
        $this->_outTemplate('example');
    }

    public function testAction()
    {
        echo $this->getLayout()->createBlock('core/profiler')->toHtml();
    }

    public function changeLocaleAction()
    {
        $locale = $this->getRequest()->getParam('locale');
        if ($locale) {
            Mage::getSingleton('adminhtml/session')->setLocale($locale);
        }
        $this->_redirectReferer();
    }

    public function deniedJsonAction()
    {
        $this->getResponse()->setBody($this->_getDeniedJson());
    }

    protected function _getDeniedJson()
    {
        return Zend_Json::encode(
            array(
                'ajaxExpired'  => 1,
                'ajaxRedirect' => $this->getUrl('*/index/login')
            )
        );
    }

    public function deniedIframeAction()
    {
        $this->getResponse()->setBody($this->_getDeniedIframe());
    }

    protected function _getDeniedIframe()
    {
        return '<script type="text/javascript">parent.window.location = \''.$this->getUrl('*/index/login').'\';</script>';
    }

    public function forgotpasswordAction ()
    {
        $email = $this->getRequest()->getParam('email');
        $params = $this->getRequest()->getParams();
        if (!empty($email) && !empty($params)) {
            $collection = Mage::getResourceModel('admin/user_collection');
            /* @var $collection Mage_Admin_Model_Mysql4_User_Collection */
            $collection->addFieldToFilter('email', $email);
            $collection->load(false);

            if ($collection->getSize() > 0) {
                foreach ($collection as $item) {
                    $user = Mage::getModel('admin/user')->load($item->getId());
                    if ($user->getId()) {
                        $pass = substr(md5(uniqid(rand(), true)), 0, 6);
                        $user->setPassword($pass);
                        $user->save();
                        $user->setPlainPassword($pass);
                        $user->sendNewPasswordEmail();
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('A new password was sent to your email address. Please check your email and click Back to Login.'));
                        $email = '';
                    }
                    break;
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Can\'t find email address.'));
            }
        } elseif (!empty($params)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Email address is empty.'));
        }


        $data = array(
            'email' => $email
        );
        $this->_outTemplate('forgotpassword', $data);
    }


    protected function _isAllowed()
    {
        /*if ( $this->getRequest()->getActionName() == 'login' && ! Mage::getSingleton('admin/session')->isAllowed('admin') ) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('You have not enought permissions to login.'));
            $request = Mage::app()->getRequest();

        } else {
            return Mage::getSingleton('admin/session')->isAllowed('admin');
        }
        */
        return true;
    }
}
