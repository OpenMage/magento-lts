<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml common tax class controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Tax_ClassController extends Mage_Adminhtml_Controller_Action
{
    /**
     * save class action
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getModel('tax/class')->setData($postData);

            try {
                $model->save();
                $classId    = $model->getId();
                $classType  = $model->getClassType();
                $classUrl   = '*/tax_class_' . strtolower($classType);

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tax')->__('The tax class has been saved.'),
                );
                $this->_redirect($classUrl);

                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setClassData($postData);
                $this->_redirectReferer();
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tax')->__('An error occurred while saving this tax class.'),
                );
                Mage::getSingleton('adminhtml/session')->setClassData($postData);
                $this->_redirectReferer();
            }

            $this->_redirectReferer();
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('*/tax_class'));
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $classType = strtolower($this->getRequest()->getParam('classType'));
        $this->loadLayout()
            ->_setActiveMenu('sales/tax/classes_' . $classType)
            ->_addBreadcrumb(Mage::helper('tax')->__('Sales'), Mage::helper('tax')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('tax')->__('Tax'), Mage::helper('tax')->__('Tax'))
        ;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/tax/classes_product')
            || Mage::getSingleton('admin/session')->isAllowed('sales/tax/classes_customer');
    }
}
