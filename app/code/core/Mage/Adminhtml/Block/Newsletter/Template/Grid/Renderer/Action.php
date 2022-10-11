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
 * Adminhtml newsletter templates grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renderer for "Action" column in Newsletter templates grid
     *
     * @param Mage_Newsletter_Model_Template|Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if($row->isValidForSend()) {
            $actions[] = [
                'url' => $this->getUrl('*/newsletter_queue/edit', ['template_id' => $row->getId()]),
                'caption' => Mage::helper('newsletter')->__('Queue Newsletter...')
            ];
        }

        $actions[] = [
            'url'     => $this->getUrl('*/*/preview', ['id'=>$row->getId()]),
            'popup'   => true,
            'caption' => Mage::helper('newsletter')->__('Preview')
        ];

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
