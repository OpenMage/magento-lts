<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $actions = [];

        if($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_NEVER) {
               if(!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
                $actions[] = [
                    'url' => $this->getUrl('*/*/start', ['id'=>$row->getId()]),
                    'caption'	=> Mage::helper('newsletter')->__('Start')
                ];
            }
        } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_SENDING) {
            $actions[] = [
                    'url' => $this->getUrl('*/*/pause', ['id'=>$row->getId()]),
                    'caption'	=>	Mage::helper('newsletter')->__('Pause')
            ];

            $actions[] = [
                'url'		=>	$this->getUrl('*/*/cancel', ['id'=>$row->getId()]),
                'confirm'	=>	Mage::helper('newsletter')->__('Do you really want to cancel the queue?'),
                'caption'	=>	Mage::helper('newsletter')->__('Cancel')
            ];

        } else if ($row->getQueueStatus()==Mage_Newsletter_Model_Queue::STATUS_PAUSE) {

            $actions[] = [
                'url' => $this->getUrl('*/*/resume', ['id'=>$row->getId()]),
                'caption'	=>	Mage::helper('newsletter')->__('Resume')
            ];

        }

        $actions[] = [
            'url'       =>  $this->getUrl('*/newsletter_queue/preview', ['id'=>$row->getId()]),
            'caption'   =>  Mage::helper('newsletter')->__('Preview'),
            'popup'     =>  true
        ];

        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
