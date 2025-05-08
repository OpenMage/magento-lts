<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter templates grid block action item renderer
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Newsletter_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renderer for "Action" column in Newsletter templates grid
     *
     * @param Mage_Newsletter_Model_Template $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        if ($row->isValidForSend()) {
            $actions[] = [
                'url' => $this->getUrl('*/newsletter_queue/edit', ['template_id' => $row->getId()]),
                'caption' => Mage::helper('newsletter')->__('Queue Newsletter...'),
            ];
        }

        $actions[] = [
            'url'     => $this->getUrl('*/*/preview', ['id' => $row->getId()]),
            'popup'   => true,
            'caption' => Mage::helper('newsletter')->__('Preview'),
        ];

        $this->getColumn()->setActions($actions);

        return parent::render($row);
    }
}
