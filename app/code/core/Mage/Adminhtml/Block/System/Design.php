<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Design extends Mage_Adminhtml_Block_Template
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('system/design/index.phtml');

        $this->setChild(
            'add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('catalog')->__('Add Design Change'),
                    'onclick'   => Mage::helper('core/js')->getSetLocationJs($this->getUrl('*/*/new')),
                    'class'     => 'add',
                ]),
        );

        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/system_design_grid', 'design.grid'));
        return parent::_prepareLayout();
    }
}
