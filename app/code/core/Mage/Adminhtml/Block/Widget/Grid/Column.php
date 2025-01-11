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
 * @copyright  Copyright (c) 2021-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grid column block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method array getActions()
 * @method $this setActions(array $value)
 * @method bool getCopyable()
 * @method $this setCopyable(bool $value)
 * @method string getDir()
 * @method array getFilterConditionCallback()
 * @method string getFilterIndex()
 * @method $this setFormat(string $value)
 * @method string getIndex()
 * @method bool getNoLink()
 * @method array getSelected()
 * @method $this setSelected(array $value)
 */
class Mage_Adminhtml_Block_Widget_Grid_Column extends Mage_Adminhtml_Block_Widget
{
    protected $_grid;
    protected $_renderer;
    protected $_filter;
    protected $_type;
    protected $_cssClass = null;

    /**
     * @param Mage_Adminhtml_Block_Widget_Grid $grid
     * @return $this
     */
    public function setGrid($grid)
    {
        $this->_grid = $grid;
        // Init filter object
        $this->getFilter();
        return $this;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    public function getGrid()
    {
        return $this->_grid;
    }

    /**
     * @return bool
     */
    public function isLast()
    {
        return $this->getId() == $this->getGrid()->getLastColumnId();
    }

    /**
     * @return string
     */
    public function getHtmlProperty()
    {
        return $this->getRenderer()->renderProperty();
    }

    /**
     * @return string
     */
    public function getHeaderHtml()
    {
        return $this->getRenderer()->renderHeader();
    }

    /**
     * @return string|null
     */
    public function getCssClass()
    {
        if (is_null($this->_cssClass)) {
            if ($this->getAlign()) {
                $this->_cssClass .= 'a-' . $this->getAlign();
            }
            // Add a custom css class for column
            if ($this->hasData('column_css_class')) {
                $this->_cssClass .= ' ' . $this->getData('column_css_class');
            }
            if ($this->getEditable()) {
                $this->_cssClass .= ' editable';
            }
            // Add css class for sorted columns
            if ($this->hasData('dir')) {
                $this->_cssClass .= ' sorted';
            }
        }

        return $this->_cssClass;
    }

    /**
     * @return string|null
     */
    public function getCssProperty()
    {
        return $this->getRenderer()->renderCss();
    }

    /**
     * @return string|null
     */
    public function getHeaderCssClass()
    {
        $class = $this->getData('header_css_class');
        if (($this->getSortable() === false) || ($this->getGrid()->getSortable() === false)) {
            $class .= ' no-link';
        }
        if ($this->isLast()) {
            $class .= ' last';
        }
        if ($this->hasData('dir')) {
            $class .= ' sorted';
        }
        return $class;
    }

    /**
     * @return string
     */
    public function getHeaderHtmlProperty()
    {
        $str = '';
        if ($class = $this->getHeaderCssClass()) {
            $str .= ' class="' . $class . '"';
        }

        return $str;
    }

    /**
     * Retrieve row column field value for display
     *
     * @return  string
     */
    public function getRowField(Varien_Object $row)
    {
        $renderedValue = $this->getRenderer()->render($row);
        if ($this->getHtmlDecorators()) {
            $renderedValue = $this->_applyDecorators($renderedValue, $this->getHtmlDecorators());
        }

        /*
         * if column has determined callback for framing call
         * it before give away rendered value
         *
         * callback_function($renderedValue, $row, $column, $isExport)
         * should return new version of rendered value
         */
        $frameCallback = $this->getFrameCallback();
        if (is_array($frameCallback)) {
            $renderedValue = call_user_func($frameCallback, $renderedValue, $row, $this, false);
        }

        if ($this->getCopyable() && $text = $this->getRenderer()->getCopyableText($row)) {
            $renderedValue = '<span data-copy-text="' . $text . '">' . $renderedValue . '</span>';
        }

        return $renderedValue;
    }

    /**
     * Retrieve row column field value for export
     *
     * @return  string
     */
    public function getRowFieldExport(Varien_Object $row)
    {
        $renderedValue = $this->getRenderer()->renderExport($row);

        /*
         * if column has determined callback for framing call
         * it before give away rendered value
         *
         * callback_function($renderedValue, $row, $column, $isExport)
         * should return new version of rendered value
         */
        $frameCallback = $this->getFrameCallback();
        if (is_array($frameCallback)) {
            $renderedValue = call_user_func($frameCallback, $renderedValue, $row, $this, true);
        }

        return $renderedValue;
    }

    /**
     * Decorate rendered cell value
     *
     * @param string $value
     * @param array|string $decorators
     * @return string
     */
    protected function &_applyDecorators($value, $decorators)
    {
        if (!is_array($decorators)) {
            if (is_string($decorators)) {
                $decorators = explode(' ', $decorators);
            }
        }
        if ((!is_array($decorators)) || empty($decorators)) {
            return $value;
        }
        switch (array_shift($decorators)) {
            case 'nobr':
                $value = '<span class="nobr">' . $value . '</span>';
                break;
        }
        if (!empty($decorators)) {
            return $this->_applyDecorators($value, $decorators);
        }
        return $value;
    }

    /**
     * @param string $renderer
     * @return $this
     */
    public function setRenderer($renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * @return string
     */
    protected function _getRendererByType()
    {
        $type = strtolower($this->getType());
        $renderers = $this->getGrid()->getColumnRenderers();

        if (is_array($renderers) && isset($renderers[$type])) {
            return $renderers[$type];
        }

        switch ($type) {
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
            case 'theme':
                $rendererClass = 'adminhtml/widget_grid_column_renderer_theme';
                break;
            default:
                $rendererClass = 'adminhtml/widget_grid_column_renderer_text';
                break;
        }
        return $rendererClass;
    }

    /**
     * Retrieve column renderer
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
     */
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

    /**
     * @param string $filterClass
     * @return void
     */
    public function setFilter($filterClass)
    {
        $this->_filter = $this->getLayout()->createBlock($filterClass)
                ->setColumn($this);
    }

    /**
     * @return string
     */
    protected function _getFilterByType()
    {
        $type = strtolower($this->getType());
        $filters = $this->getGrid()->getColumnFilters();
        if (is_array($filters) && isset($filters[$type])) {
            return $filters[$type];
        }

        switch ($type) {
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
            case 'theme':
                $filterClass = 'adminhtml/widget_grid_column_filter_theme';
                break;
            default:
                $filterClass = 'adminhtml/widget_grid_column_filter_text';
                break;
        }
        return $filterClass;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract|false
     */
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

    /**
     * @return string
     */
    public function getFilterHtml()
    {
        if ($this->getFilter()) {
            return $this->getFilter()->getHtml();
        }

        return '&nbsp;';
    }

    /**
     * Retrieve Header Name for Export
     *
     * @return string
     */
    public function getExportHeader()
    {
        if ($this->getHeaderExport()) {
            return $this->getHeaderExport();
        }
        return $this->getHeader();
    }

    public function getType(): string
    {
        return (string) $this->_getData('type');
    }
}
