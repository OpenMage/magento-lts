<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Grid column widget for rendering action grid cells
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Renders column
     *
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $actions = $this->getColumn()->getActions();
        if (empty($actions) || !is_array($actions)) {
            return '&nbsp;';
        }

        if (count($actions) === 1 && !$this->getColumn()->getNoLink()) {
            foreach ($actions as $action) {
                if (is_array($action)) {
                    return $this->_toLinkHtml($action, $row);
                }
            }
        }

        $out = '<select class="action-select" onchange="varienGridAction.execute(this);">'
             . '<option value=""></option>';
        $i = 0;
        foreach ($actions as $action) {
            $i++;
            if (is_array($action)) {
                $out .= $this->_toOptionHtml($action, $row);
            }
        }
        return $out . '</select>';
    }

    /**
     * Render single action as dropdown option html
     *
     * @param array $action
     * @return string
     */
    protected function _toOptionHtml($action, Varien_Object $row)
    {
        $actionAttributes = new Varien_Object();

        $actionCaption = '';
        $this->_transformActionData($action, $actionCaption, $row);

        $htmlAttibutes = ['value' => $this->escapeHtml(Mage::helper('core')->jsonEncode($action))];
        $actionAttributes->setData($htmlAttibutes);
        return '<option ' . $actionAttributes->serialize() . '>' . $actionCaption . '</option>';
    }

    /**
     * Render single action as link html
     *
     * @param array $action
     * @return string
     */
    protected function _toLinkHtml($action, Varien_Object $row)
    {
        $actionAttributes = new Varien_Object();

        $actionCaption = '';
        $this->_transformActionData($action, $actionCaption, $row);

        if (isset($action['confirm'])) {
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
     * @return $this
     */
    protected function _transformActionData(&$action, &$actionCaption, Varien_Object $row)
    {
        foreach (array_keys($action) as $attribute) {
            if (isset($action[$attribute]) && !is_array($action[$attribute])) {
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
                    if (is_array($action['url'])) {
                        $params = [$action['field'] => $this->_getValue($row)];
                        if (isset($action['url']['params'])) {
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
