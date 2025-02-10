<?php
/**
 * Adminhtml newsletter templates grid block action item renderer
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_System_Email_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $actions = [];

        $actions[] = [
            'url'       =>  $this->getUrl('*/*/preview', ['id' => $row->getId()]),
            'popup'     =>  true,
            'caption'   =>  $this->__('Preview'),
        ];

        $this->getColumn()->setActions($actions);

        return parent::render($row);
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
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }
}
