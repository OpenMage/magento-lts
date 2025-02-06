<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */

/**
 * Adminhtml customers wishlist grid item action renderer for few action controls in one cell
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Customer_Grid_Renderer_Multiaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * Renders column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $html = '';
        $actions = $this->getColumn()->getActions();
        if (!empty($actions) && is_array($actions)) {
            $links = [];
            foreach ($actions as $action) {
                if (is_array($action)) {
                    $link = $this->_toLinkHtml($action, $row);
                    if ($link) {
                        $links[] = $link;
                    }
                }
            }
            $html = implode('<br />', $links);
        }

        if ($html == '') {
            $html = '&nbsp;';
        }

        return $html;
    }

    /**
     * Render single action as link html
     *
     * @param  array $action
     * @return string
     */
    protected function _toLinkHtml($action, Varien_Object $row)
    {
        $product = $row->getProduct();

        if (isset($action['process']) && $action['process'] == 'configurable') {
            if ($product->canConfigure()) {
                $style = '';
                $onClick = sprintf('onclick="return %s.configureItem(%s)"', $action['control_object'], $row->getId());
            } else {
                $style = 'style="color: #CCC;"';
                $onClick = '';
            }

            return sprintf('<a href="%s" %s %s>%s</a>', $action['url'], $style, $onClick, $action['caption']);
        } else {
            return parent::_toLinkHtml($action, $row);
        }
    }
}
