<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Admin ratings controller
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_RatingController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'catalog/reviews_ratings/ratings';

    public function indexAction()
    {
        $this->_initEnityId();
        $this->loadLayout();

        $this->_setActiveMenu('catalog/reviews_ratings/ratings');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Ratings'), Mage::helper('adminhtml')->__('Manage Ratings'));
        $this->_addContent($this->getLayout()->createBlock('adminhtml/rating_rating'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_initEnityId();
        $this->loadLayout();

        $ratingModel = Mage::getModel('rating/rating');
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }

        $this->_title($ratingModel->getId() ? $ratingModel->getRatingCode() : $this->__('New Rating'));

        $this->_setActiveMenu('catalog/reviews_ratings/ratings');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Ratings'), Mage::helper('adminhtml')->__('Manage Ratings'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/rating_edit'))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/rating_edit_tabs'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save rating
     */
    public function saveAction()
    {
        $this->_initEnityId();

        if ($this->getRequest()->getPost()) {
            try {
                $ratingModel = Mage::getModel('rating/rating');

                $stores = $this->getRequest()->getParam('stores');
                $position = (int) $this->getRequest()->getParam('position');
                $stores[] = 0;
                $ratingModel->setRatingCode($this->getRequest()->getParam('rating_code'))
                    ->setRatingCodes($this->getRequest()->getParam('rating_codes'))
                    ->setStores($stores)
                    ->setPosition($position)
                    ->setId($this->getRequest()->getParam('id'))
                    ->setEntityId(Mage::registry('entityId'))
                    ->save();

                $options = $this->getRequest()->getParam('option_title');

                if (is_array($options)) {
                    $i = 1;
                    foreach ($options as $key => $optionCode) {
                        $optionModel = Mage::getModel('rating/rating_option');
                        if (!preg_match('/^add_(\d*?)$/', $key)) {
                            $optionModel->setId($key);
                        }

                        $optionModel->setCode($optionCode)
                            ->setValue($i)
                            ->setRatingId($ratingModel->getId())
                            ->setPosition($i)
                            ->save();
                        $i++;
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The rating has been saved.'));
                Mage::getSingleton('adminhtml/session')->setRatingData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setRatingData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('rating/rating');
                /** @var Mage_Rating_Model_Rating $model */
                $model->load($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The rating has been deleted.'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        $this->_redirect('*/*/');
    }

    protected function _initEnityId()
    {
        $this->_title($this->__('Catalog'))
             ->_title($this->__('Reviews and Ratings'))
             ->_title($this->__('Manage Ratings'));

        Mage::register('entityId', Mage::getModel('rating/rating_entity')->getIdByCode('product'));
    }
}
