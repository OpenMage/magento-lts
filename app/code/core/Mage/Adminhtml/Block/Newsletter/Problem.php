<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter problem block template.
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Problem extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('newsletter/problem/list.phtml');
        $collection = Mage::getResourceSingleton('newsletter/problem_collection')
            ->addSubscriberInfo()
            ->addQueueInfo();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('adminhtml/newsletter_problem_grid', 'newsletter.problem.grid'),
        );

        $this->setChild(
            'deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button', 'del.button')
                ->setData(
                    [
                        'label' => Mage::helper('newsletter')->__('Delete Selected Problems'),
                        'onclick' => 'problemController.deleteSelected();',
                    ],
                ),
        );

        $this->setChild(
            'unsubscribeButton',
            $this->getLayout()->createBlock('adminhtml/widget_button', 'unsubscribe.button')
                ->setData(
                    [
                        'label' => Mage::helper('newsletter')->__('Unsubscribe Selected'),
                        'onclick' => 'problemController.unsubscribe();',
                    ],
                ),
        );
        return parent::_prepareLayout();
    }

    public function getUnsubscribeButtonHtml()
    {
        return $this->getChildHtml('unsubscribeButton');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }

    public function getShowButtons()
    {
        return  Mage::getResourceSingleton('newsletter/problem_collection')->getSize() > 0;
    }
}
