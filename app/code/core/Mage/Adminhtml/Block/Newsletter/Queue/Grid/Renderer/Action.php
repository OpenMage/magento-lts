<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * @param Mage_Newsletter_Model_Queue $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = [];

        if ($row->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            if (!$row->getQueueStartAt() && $row->getSubscribersTotal()) {
                $actions[] = [
                    'url'       => $this->getUrl('*/*/start', ['id' => $row->getId()]),
                    'caption'   => Mage::helper('newsletter')->__('Start')
                ];
            }
        } elseif ($row->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_SENDING) {
            $actions[] = [
                    'url'       => $this->getUrl('*/*/pause', ['id' => $row->getId()]),
                    'caption'   =>  Mage::helper('newsletter')->__('Pause')
            ];

            $actions[] = [
                'url'       =>  $this->getUrl('*/*/cancel', ['id' => $row->getId()]),
                'confirm'   =>  Mage::helper('newsletter')->__('Do you really want to cancel the queue?'),
                'caption'   =>  Mage::helper('newsletter')->__('Cancel')
            ];
        } elseif ($row->getQueueStatus() == Mage_Newsletter_Model_Queue::STATUS_PAUSE) {
            $actions[] = [
                'url'       => $this->getUrl('*/*/resume', ['id' => $row->getId()]),
                'caption'   =>  Mage::helper('newsletter')->__('Resume')
            ];
        }

        $actions[] = [
            'url'       =>  $this->getUrl('*/newsletter_queue/preview', ['id' => $row->getId()]),
            'caption'   =>  Mage::helper('newsletter')->__('Preview'),
            'popup'     =>  true
        ];

        $this->getColumn()->setActions($actions);
        return parent::render($row);
    }
}
