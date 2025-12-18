<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Grid column block
 *
 * @package    Mage_Adminhtml
 *
 * @method array getActions()
 * @method bool getCopyable()
 * @method string getDir()
 * @method array getFilterConditionCallback()
 * @method string getFilterIndex()
 * @method string getIndex()
 * @method bool getNoLink()
 * @method array getSelected()
 * @method $this setActions(array $value)
 * @method $this setCopyable(bool $value)
 * @method $this setFormat(string $value)
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
     * @return null|string
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
     * @return null|string
     */
    public function getCssProperty()
    {
        return $this->getRenderer()->renderCss();
    }

    /**
     * @return null|string
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

        if (empty($decorators)) {
            return $value;
        }

        if (array_shift($decorators) === 'nobr') {
            $value = '<span class="nobr">' . $value . '</span>';
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

        return match ($type) {
            'date' => 'adminhtml/widget_grid_column_renderer_date',
            'datetime' => 'adminhtml/widget_grid_column_renderer_datetime',
            'number' => 'adminhtml/widget_grid_column_renderer_number',
            'currency' => 'adminhtml/widget_grid_column_renderer_currency',
            'price' => 'adminhtml/widget_grid_column_renderer_price',
            'country' => 'adminhtml/widget_grid_column_renderer_country',
            'concat' => 'adminhtml/widget_grid_column_renderer_concat',
            'action' => 'adminhtml/widget_grid_column_renderer_action',
            'options' => 'adminhtml/widget_grid_column_renderer_options',
            'checkbox' => 'adminhtml/widget_grid_column_renderer_checkbox',
            'massaction' => 'adminhtml/widget_grid_column_renderer_massaction',
            'radio' => 'adminhtml/widget_grid_column_renderer_radio',
            'input' => 'adminhtml/widget_grid_column_renderer_input',
            'select' => 'adminhtml/widget_grid_column_renderer_select',
            'text' => 'adminhtml/widget_grid_column_renderer_longtext',
            'store' => 'adminhtml/widget_grid_column_renderer_store',
            'wrapline' => 'adminhtml/widget_grid_column_renderer_wrapline',
            'theme' => 'adminhtml/widget_grid_column_renderer_theme',
            default => 'adminhtml/widget_grid_column_renderer_text',
        };
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

        return match ($type) {
            'datetime' => 'adminhtml/widget_grid_column_filter_datetime',
            'date' => 'adminhtml/widget_grid_column_filter_date',
            'range', 'number', 'currency' => 'adminhtml/widget_grid_column_filter_range',
            'price' => 'adminhtml/widget_grid_column_filter_price',
            'country' => 'adminhtml/widget_grid_column_filter_country',
            'options' => 'adminhtml/widget_grid_column_filter_select',
            'massaction' => 'adminhtml/widget_grid_column_filter_massaction',
            'checkbox' => 'adminhtml/widget_grid_column_filter_checkbox',
            'radio' => 'adminhtml/widget_grid_column_filter_radio',
            'store' => 'adminhtml/widget_grid_column_filter_store',
            'theme' => 'adminhtml/widget_grid_column_filter_theme',
            default => 'adminhtml/widget_grid_column_filter_text',
        };
    }

    /**
     * @return false|Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Abstract
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
