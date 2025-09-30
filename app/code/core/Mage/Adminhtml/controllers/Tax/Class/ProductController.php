<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml product tax class controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Tax_Class_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'sales/tax/classes_product';

    /**
     * view grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Product Tax Classes'));

        $this->_initAction()
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_class')
                    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
            )
            ->renderLayout();
    }

    /**
     * new class action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * edit class action
     */
    public function editAction()
    {
        $this->_title($this->__('Sales'))
             ->_title($this->__('Tax'))
             ->_title($this->__('Product Tax Classes'));

        $classId    = $this->getRequest()->getParam('id');
        $model      = Mage::getModel('tax/class');
        if ($classId) {
            $model->load($classId);
            if (!$model->getId() || $model->getClassType() != Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('tax')->__('This class no longer exists'),
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getClassName() : $this->__('New Class'));

        $data = Mage::getSingleton('adminhtml/session')->getClassData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('tax_class', $model);

        $this->_initAction()
            ->_addBreadcrumb(
                $classId ? Mage::helper('tax')->__('Edit Class') : Mage::helper('tax')->__('New Class'),
                $classId ? Mage::helper('tax')->__('Edit Class') : Mage::helper('tax')->__('New Class'),
            )
            ->_addContent(
                $this->getLayout()->createBlock('adminhtml/tax_class_edit')
                    ->setData('action', $this->getUrl('*/tax_class/save'))
                    ->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
            )
            ->renderLayout();
    }

    /**
     * delete class action
     */
    public function deleteAction()
    {
        $classId    = $this->getRequest()->getParam('id');
        $session    = Mage::getSingleton('adminhtml/session');
        $classModel = Mage::getModel('tax/class')
            ->load($classId);

        if (!$classModel->getId() || $classModel->getClassType() != Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT) {
            $session->addError(Mage::helper('tax')->__('This class no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        $ruleCollection = Mage::getModel('tax/calculation_rule')
            ->getCollection()
            ->setClassTypeFilter(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT, $classId);

        if ($ruleCollection->getSize() > 0) {
            $session->addError(Mage::helper('tax')->__('You cannot delete this tax class as it is used in Tax Rules. You have to delete the rules it is used in first.'));
            $this->_redirect('*/*/edit/', ['id' => $classId]);
            return;
        }

        $productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('tax_class_id', $classId);
        $productCount = $productCollection->getSize();

        if ($productCount > 0) {
            $session->addError(Mage::helper('tax')->__('You cannot delete this tax class as it is used for %d products.', $productCount));
            $this->_redirect('*/*/edit/', ['id' => $classId]);
            return;
        }

        try {
            $classModel->delete();

            $session->addSuccess(Mage::helper('tax')->__('The tax class has been deleted.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            return;
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('tax')->__('An error occurred while deleting this tax class.'));
        }

        $this->_redirect('*/*/edit/', ['id' => $classId]);
    }

    /**
     * Initialize action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/tax/classes_product')
            ->_addBreadcrumb(Mage::helper('tax')->__('Sales'), Mage::helper('tax')->__('Sales'))
            ->_addBreadcrumb(Mage::helper('tax')->__('Tax'), Mage::helper('tax')->__('Tax'))
            ->_addBreadcrumb(Mage::helper('tax')->__('Manage Product Tax Classes'), Mage::helper('tax')->__('Manage Product Tax Classes'))
        ;
        return $this;
    }
}
