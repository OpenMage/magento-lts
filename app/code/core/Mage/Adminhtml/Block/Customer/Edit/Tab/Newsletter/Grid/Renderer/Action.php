<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml newsletter queue grid block action item renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $actions = [];

        $actions[] = [
            '@' =>  [
                'href'  => $this->getUrl(
                    '*/newsletter_template/preview',
                    [
                        'id'        => $row->getTemplateId(),
                        'subscriber' => Mage::registry('subscriber')->getId(),
                    ],
                ),
                'target' =>  '_blank',
            ],
            '#' => Mage::helper('customer')->__('View'),
        ];

        return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value), '\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        $html = [];
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode('<span class="separator">&nbsp;|&nbsp;</span>', $html);
    }
}
