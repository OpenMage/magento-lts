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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter problem block template.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $this->setChild('grid',
            $this->getLayout()->createBlock('adminhtml/newsletter_problem_grid','newsletter.problem.grid')
        );

        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('adminhtml/widget_button','del.button')
                ->setData(
                    array(
                        'label' => Mage::helper('newsletter')->__('Delete Selected Problems'),
                        'onclick' => 'problemController.deleteSelected();'
                    )
                )
        );

        $this->setChild('unsubscribeButton',
            $this->getLayout()->createBlock('adminhtml/widget_button','unsubscribe.button')
                ->setData(
                    array(
                        'label' => Mage::helper('newsletter')->__('Unsubscribe Selected'),
                        'onclick' => 'problemController.unsubscribe();'
                    )
                )
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

}// Class Mage_Adminhtml_Block_Newsletter_Problem END
