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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid column widget for rendering action grid cells
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{

    /**
     * Renders column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if ( empty($actions) || !is_array($actions) ) {
            return '&nbsp;';
        }

        if(count($actions) === 1 && !$this->getColumn()->getNoLink()) {
            foreach ($actions as $action) {
                if ( is_array($action) ) {
                    return $this->_toLinkHtml($action, $row);
                }
            }
        }

        $out = '<select class="action-select" onchange="varienGridAction.execute(this);">'
             . '<option value=""></option>';
        $i = 0;
        foreach ($actions as $action){
            $i++;
            if ( is_array($action) ) {
                $out .= $this->_toOptionHtml($action, $row);
            }
        }
        $out .= '</select>';
        return $out;
    }

    /**
     * Render single action as dropdown option html
     *
     * @param unknown_type $action
     * @param Varien_Object $row
     * @return string
     */
    protected function _toOptionHtml($action, Varien_Object $row)
    {
        $actionAttributes = new Varien_Object();

        $actionCaption = '';
        $this->_transformActionData($action, $actionCaption, $row);

        $htmlAttibutes = array('value'=>$this->escapeHtml(Mage::helper('core')->jsonEncode($action)));
        $actionAttributes->setData($htmlAttibutes);
        return '<option ' . $actionAttributes->serialize() . '>' . $actionCaption . '</option>';
    }

    /**
     * Render single action as link html
     *
     * @param array $action
     * @param Varien_Object $row
     * @return string
     */
    protected function _toLinkHtml($action, Varien_Object $row)
    {
        $actionAttributes = new Varien_Object();

        $actionCaption = '';
        $this->_transformActionData($action, $actionCaption, $row);

        if(isset($action['confirm'])) {
            $action['onclick'] = 'return window.confirm(\''
                               . addslashes($this->escapeHtml($action['confirm']))
                               . '\')';
            unset($action['confirm']);
        }

        $actionAttributes->setData($action);
        return '<a ' . $actionAttributes->serialize() . '>' . $actionCaption . '</a>';
    }

    /**
     * Prepares action data for html render
     *
     * @param array $action
     * @param string $actionCaption
     * @param Varien_Object $row
     * @return $this
     */
    protected function _transformActionData(&$action, &$actionCaption, Varien_Object $row)
    {
        foreach ( $action as $attribute => $value ) {
            if(isset($action[$attribute]) && !is_array($action[$attribute])) {
                $this->getColumn()->setFormat($action[$attribute]);
                $action[$attribute] = parent::render($row);
            } else {
                $this->getColumn()->setFormat(null);
            }

            switch ($attribute) {
                case 'caption':
                    $actionCaption = $action['caption'];
                    unset($action['caption']);
                       break;

                case 'url':
                    if(is_array($action['url'])) {
                        $params = array($action['field']=>$this->_getValue($row));
                        if(isset($action['url']['params'])) {
                            $params = array_merge($action['url']['params'], $params);
                        }
                        $action['href'] = $this->getUrl($action['url']['base'], $params);
                        unset($action['field']);
                    } else {
                        $action['href'] = $action['url'];
                    }
                    unset($action['url']);
                       break;

                case 'popup':
                    $action['onclick'] =
                        'popWin(this.href,\'_blank\',\'width=800,height=700,resizable=1,scrollbars=1\');return false;';
                    break;

            }
        }
        return $this;
    }
}
