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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid column block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Column extends Mage_Adminhtml_Block_Widget
{
    protected $_grid;
    protected $_renderer;
    protected $_filter;
    protected $_type;
    protected $_cssClass=null;

    public function __construct($data=array())
    {
        parent::__construct($data);
    }

    public function setGrid($grid)
    {
        $this->_grid = $grid;
        // Init filter object
        $this->getFilter();
        return $this;
    }

    public function getGrid()
    {
        return $this->_grid;
    }

    public function isLast()
    {
        return $this->getId() == $this->getGrid()->getLastColumnId();
    }

    public function getHtmlProperty()
    {
        return $this->getRenderer()->renderProperty();
    }

    public function getHeaderHtml()
    {
        return $this->getRenderer()->renderHeader();
    }

    public function getCssClass()
    {
        if (is_null($this->_cssClass)) {
            if ($this->getAlign()) {
                $this->_cssClass .= 'a-'.$this->getAlign();
            }
            // Add a custom css class for column
            if ($this->hasData('column_css_class')) {
                $this->_cssClass .= ' '. $this->getData('column_css_class');
            }
        }

        return $this->_cssClass;
    }

    public function getCssProperty()
    {
        return $this->getRenderer()->renderCss();
    }

    public function getHeaderCssClass()
    {
        $class = $this->getData('header_css_class');
        if (($this->getSortable()===false) || ($this->getGrid()->getSortable()===false)) {
            $class .= 'no-link';
        }
        if ($this->isLast()) {
            $class .= ' last';
        }
        return $class;
    }

    public function getHeaderHtmlProperty()
    {
        $str = '';
        if ($class = $this->getHeaderCssClass()) {
            $str.= ' class="'.$class.'"';
        }
        if ($this->getEditable()) {
            $str.= ' colspan="2"';
        }

        return $str;
    }

    /**
     * Retrieve row column field value for display
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function getRowField(Varien_Object $row)
    {
        return $this->getRenderer()->render($row);
    }

    public function setRenderer($renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    protected function _getRendererByType()
    {
        switch (strtolower($this->getType())) {
            case 'date':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_date';
                break;
            case 'datetime':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_datetime';
                break;
            case 'number':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_number';
                break;
            case 'currency':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_currency';
                break;
            case 'price':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_price';
                break;
            case 'country':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_country';
                break;
            case 'concat':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_concat';
                break;
            case 'action':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_action';
                break;
            case 'options':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_options';
                break;
            case 'checkbox':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_checkbox';
                break;
            case 'massaction':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_massaction';
                break;
            case 'radio':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_radio';
                break;
            case 'input':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_input';
                break;
            case 'select':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_select';
                break;
            case 'text':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_longtext';
                break;
            case 'store':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_store';
                break;
            case 'wrapline':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_wrapline';
                break;
            default:
                $rendererClass = 'adminhtml/widget_grid_column_renderer_text';
                break;
        }
        return $rendererClass;
    }

    public function getRenderer()
    {
        if (!$this->_renderer) {
            $rendererClass = $this->getData('renderer');
            if (!$rendererClass) {
                $rendererClass = $this->_getRendererByType();
            }
            $this->_renderer = $this->getLayout()->createBlock($rendererClass)
                ->setColumn($this);
        }
        return $this->_renderer;
    }

    public function setFilter($column)
    {
    }

    protected function _getFilterByType()
    {
        switch (strtolower($this->getType())) {
            case 'datetime':
                $filterClass = 'adminhtml/widget_grid_column_filter_datetime';
                break;
            case 'date':
                $filterClass = 'adminhtml/widget_grid_column_filter_date';
                break;
            case 'range':
            case 'number':
            case 'currency':
                $filterClass = 'adminhtml/widget_grid_column_filter_range';
                break;
            case 'price':
                $filterClass = 'adminhtml/widget_grid_column_filter_price';
                break;
            case 'country':
                $filterClass = 'adminhtml/widget_grid_column_filter_country';
                break;
            case 'options':
                $filterClass = 'adminhtml/widget_grid_column_filter_select';
                break;

            case 'massaction':
                $filterClass = 'adminhtml/widget_grid_column_filter_massaction';
                break;

            case 'checkbox':
                $filterClass = 'adminhtml/widget_grid_column_filter_checkbox';
                break;

            case 'radio':
                $filterClass = 'adminhtml/widget_grid_column_filter_radio';
                break;
            case 'store':
                $filterClass = 'adminhtml/widget_grid_column_filter_store';
                break;
            default:
                $filterClass = 'adminhtml/widget_grid_column_filter_text';
                break;
        }
        return $filterClass;
    }

    public function getFilter()
    {
        if (!$this->_filter) {
            $filterClass = $this->getData('filter');
            if ($filterClass === false) {
                return false;
            }
            if (!$filterClass) {
                $filterClass = $this->_getFilterByType();
                if ($filterClass === false) {
                    return false;
                }
            }
            $this->_filter = $this->getLayout()->createBlock($filterClass)
                ->setColumn($this);
        }

        return $this->_filter;
    }

    public function getFilterHtml()
    {
        if ($this->getFilter()) {
            return $this->getFilter()->getHtml();
        } else {
            return '&nbsp;';
        }
        return null;
    }
}