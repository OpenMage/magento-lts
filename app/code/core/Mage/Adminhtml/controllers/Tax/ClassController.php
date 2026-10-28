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
     * @return void
     */
    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {
            $model = Mage::getModel('tax/class')->setData($postData);

            try {
                $model->save();
                $classType = $model->getClassType();
                $classUrl  = '*/tax_class_' . strtolower($classType);

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('tax')->__('The tax class has been saved.'),
                );
                $this->_redirect($classUrl);

                return;
            } catch (Mage_Core_Exception $mageCoreException) {
                Mage::getSingleton('adminhtml/session')->addError($mageCoreException->getMessage());
                Mage::getSingleton('adminhtml/session')->setClassData($postData);
                $this->_redirectReferer();
            } catch (Exception) {
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
     * @return $this
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
    #[Override]
    protected function _isAllowed(): bool
    {
        $allowedTypes = $this->_getAllowedClassTypes();
        if ($allowedTypes === []) {
            return false;
        }

        $request = $this->getRequest();
        if (strtolower($request->getActionName()) !== 'save' || !$request->isPost()) {
            return true;
        }

        // The save endpoint is shared by both class types, so authorize the type being written
        $types = [];
        if ($request->getPost('class_type') !== null) {
            $types[] = (string) $request->getPost('class_type');
        }

        $classId = (int) $request->getPost('class_id');
        if ($classId > 0) {
            $types[] = (string) $this->_getClassType($classId);
        }

        if ($types === []) {
            return false;
        }

        return array_diff($types, $allowedTypes) === [];
    }

    /**
     * Tax class types the current admin may manage
     *
     * @return string[]
     */
    protected function _getAllowedClassTypes(): array
    {
        $session = Mage::getSingleton('admin/session');
        $types = [];
        if ($session->isAllowed('sales/tax/classes_product')) {
            $types[] = Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT;
        }

        if ($session->isAllowed('sales/tax/classes_customer')) {
            $types[] = Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER;
        }

        return $types;
    }

    /**
     * Type of an existing tax class, or null when it does not exist
     */
    protected function _getClassType(int $classId): ?string
    {
        $class = Mage::getModel('tax/class')->load($classId);
        return $class->getId() === null ? null : (string) $class->getClassType();
    }
}
