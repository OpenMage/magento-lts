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
 * @copyright  Copyright (c) 2018-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml grid widget block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 * @method $this setSortable(bool $value)
 * @method $this setUseAjax(bool $value)
 */
class Mage_Adminhtml_Block_Widget_Grid extends Mage_Adminhtml_Block_Widget
{
    /**
     * Columns array
     *
     * array(
     *      'header'    => string,
     *      'width'     => int,
     *      'sortable'  => bool,
     *      'index'     => string,
     *      //'renderer'  => Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Interface,
     *      'format'    => string
     *      'total'     => string (sum, avg)
     * )
     * @var array
     */
    protected $_columns = [];

    protected $_lastColumnId;

    /**
     * Collection object
     *
     * @var Varien_Data_Collection_Db|null
     */
    protected $_collection = null;

    /**
     * Page and sorting var names
     *
     * @var string
     */
    protected $_varNameLimit    = 'limit';
    protected $_varNamePage     = 'page';
    protected $_varNameSort     = 'sort';
    protected $_varNameDir      = 'dir';
    protected $_varNameFilter   = 'filter';

    protected $_defaultLimit    = 20;
    protected $_defaultPage     = 1;
    protected $_defaultSort     = false;
    protected $_defaultDir      = 'desc';
    protected $_defaultFilter   = [];

    /**
     * Export flag
     *
     * @var bool
     */
    protected $_isExport = false;

    /**
     * Empty grid text
     *
     * @var string|null
     */
    protected $_emptyText;

    /**
     * Empty grid text CSS class
     *
     * @var string|null
     */
    protected $_emptyTextCss    = 'a-center';

    /**
     * Pager visibility
     *
     * @var bool
     */
    protected $_pagerVisibility = true;

    /**
     * Column headers visibility
     *
     * @var bool
     */
    protected $_headersVisibility = true;

    /**
     * Filter visibility
     *
     * @var bool
     */
    protected $_filterVisibility = true;

    /**
     * Massage block visibility
     *
     * @var bool
     */
    protected $_messageBlockVisibility = false;

    protected $_saveParametersInSession = false;

    /**
     * Count totals
     *
     * @var bool
     */
    protected $_countTotals = false;

    /**
     * Count subtotals
     *
     * @var bool
     */
    protected $_countSubTotals = false;

    /**
     * Totals
     *
     * @var Varien_Object
     */
    protected $_varTotals;

    /**
     * SubTotals
     *
     * @var array
     */
    protected $_subtotals = [];

    /**
     * Grid export types
     *
     * @var array
     */
    protected $_exportTypes = [];

    /**
     * Rows per page for import
     *
     * @var int
     */
    protected $_exportPageSize = 1000;

    /**
     * Massaction row id field
     *
     * @var string
     */
    protected $_massactionIdField = null;

    /**
     * Massaction row id filter
     *
     * @var string
     */
    protected $_massactionIdFilter = null;

    /**
     * Massaction block name
     *
     * @var string
     */
    protected $_massactionBlockName = 'adminhtml/widget_grid_massaction';

    /**
    * RSS list
    *
    * @var array
    */
    protected $_rssLists = [];

    /**
     * Columns view order
     *
     * @var array
     */
    protected $_columnsOrder = [];

    /**
     * Columns to group by
     *
     * @var array
     */
    protected $_groupedColumn = [];

    /**
     * Label for empty cell
     *
     * @var string
     */
    protected $_emptyCellLabel = '';

    /**
     * @var null|array[][]
     */
    protected ?array $defaultColumnSettings = null;

    /**
     * Mage_Adminhtml_Block_Widget_Grid constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $this->setTemplate('widget/grid.phtml');
        $this->setRowClickCallback('openGridRow');
        $this->_emptyText = Mage::helper('adminhtml')->__('No records found.');
    }

    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'export_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Export'),
                    'onclick'   => $this->getJsObjectName() . '.doExport()',
                    'class'   => 'task',
                ]),
        );
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Reset Filter'),
                    'onclick'   => $this->getJsObjectName() . '.resetFilter()',
                ]),
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label'     => Mage::helper('adminhtml')->__('Search'),
                    'onclick'   => $this->getJsObjectName() . '.doFilter()',
                    'class'   => 'task',
                ]),
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getExportButtonHtml()
    {
        return $this->getChildHtml('export_button');
    }

    /**
     * @return string
     */
    public function getResetFilterButtonHtml()
    {
        return $this->getChildHtml('reset_filter_button');
    }

    /**
     * @return string
     */
    public function getSearchButtonHtml()
    {
        return $this->getChildHtml('search_button');
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if ($this->getFilterVisibility()) {
            $html .= $this->getResetFilterButtonHtml();
            $html .= $this->getSearchButtonHtml();
        }
        return $html;
    }

    /**
     * set collection object
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract|Varien_Data_Collection_Db $collection
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
    }

    /**
     * get collection object
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|Varien_Data_Collection_Db
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Add column to grid
     *
     * @param string $columnId
     * @param array $column
     * @return $this
     * @throws Exception
     */
    public function addColumn($columnId, $column)
    {
        if (is_array($column)) {
            $column = $this->addColumnDefaultData($column);
            $this->_columns[$columnId] = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
                ->setData($column)
                ->setGrid($this);
        } else {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong column format.'));
        }

        $this->_columns[$columnId]->setId($columnId);
        $this->_lastColumnId = $columnId;
        return $this;
    }

    public function addColumnDefaultData(array $column): array
    {
        $config = $this->getConfigDefaultColumnSettings();
        $columnHasIndex = array_key_exists('index', $column);
        if ($columnHasIndex &&
            !is_array($column['index']) &&
            array_key_exists($column['index'], $config['index'])
        ) {
            $column += $config['index'][$column['index']];
        }

        $columnHasType = array_key_exists('type', $column);
        if ($columnHasType && array_key_exists($column['type'], $config['type'])) {
            $column += $config['type'][$column['type']];
        }

        if ($columnHasType
            && !array_key_exists('header', $column)
            && in_array($column['type'], ['action', 'currency', 'price', 'store'])
        ) {
            switch ($column['type']) {
                case 'action':
                    $column['header'] = Mage::helper('adminhtml')->__('Action');
                    break;
                case 'currency':
                case 'price':
                    $column['header'] = Mage::helper('adminhtml')->__('Price');
                    break;
                case 'store':
                    $column['header'] = Mage::helper('adminhtml')->__('Store View');
                    break;
            }
        }

        return $column;
    }

    public function getConfigDefaultColumnSettings(): array
    {
        if (is_null($this->defaultColumnSettings)) {
            $configNode = Mage::getConfig()->getNode('grid/column/default');
            # should be called only once
            if ($configNode === false) {
                Mage::app()->getCacheInstance()->cleanType('config');
                $configNode = Mage::getConfig()->getNode('grid/column/default');
            }
            $config = $configNode->asArray();
            array_walk_recursive($config, function (&$value, $key) {
                $boolean = ['display_deleted', 'filter', 'sortable', 'store_view'];
                if (in_array($key, $boolean)) {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                }
            });
            $this->defaultColumnSettings = $config;
        }

        return $this->defaultColumnSettings;
    }

    /**
     * Remove existing column
     *
     * @param string $columnId
     * @return $this
     */
    public function removeColumn($columnId)
    {
        if (isset($this->_columns[$columnId])) {
            unset($this->_columns[$columnId]);
            if ($this->_lastColumnId == $columnId) {
                $this->_lastColumnId = key($this->_columns);
            }
        }
        return $this;
    }

    /**
     * Add column to grid after specified column.
     *
     * @param string $columnId
     * @param array|Varien_Object $column
     * @param string $after
     * @return $this
     * @throws Exception
     */
    public function addColumnAfter($columnId, $column, $after)
    {
        $this->addColumn($columnId, $column);
        $this->addColumnsOrder($columnId, $after);
        return $this;
    }

    /**
     * Add column view order
     *
     * @param string $columnId
     * @param string $after
     * @return $this
     */
    public function addColumnsOrder($columnId, $after)
    {
        $this->_columnsOrder[$columnId] = $after;
        return $this;
    }

    /**
     * Retrieve columns order
     *
     * @return array
     */
    public function getColumnsOrder()
    {
        return $this->_columnsOrder;
    }

    /**
     * Sort columns by predefined order
     *
     * @return $this
     */
    public function sortColumnsByOrder()
    {
        $keys = array_keys($this->_columns);
        $values = array_values($this->_columns);

        foreach ($this->getColumnsOrder() as $columnId => $after) {
            if (in_array($after, $keys)) {
                // Moving grid column
                $positionCurrent = array_search($columnId, $keys);

                $key = array_splice($keys, $positionCurrent, 1);
                $value = array_splice($values, $positionCurrent, 1);

                $positionTarget = array_search($after, $keys) + 1;

                array_splice($keys, $positionTarget, 0, $key);
                array_splice($values, $positionTarget, 0, $value);

                $this->_columns = array_combine($keys, $values);
            }
        }
        $this->_lastColumnId = array_key_last($this->_columns);
        return $this;
    }

    public function getLastColumnId()
    {
        return $this->_lastColumnId;
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return count($this->getColumns());
    }

    /**
     * Retrieve grid column by column id
     *
     * @param   string $columnId
     * @return  Mage_Adminhtml_Block_Widget_Grid_Column|false
     */
    public function getColumn($columnId)
    {
        if (!empty($this->_columns[$columnId])) {
            return $this->_columns[$columnId];
        }
        return false;
    }

    /**
     * Retrieve all grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Column[]
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    protected function _setFilterValues($data)
    {
        foreach (array_keys($data) as $columnId) {
            $column = $this->getColumn($columnId);
            if ($column instanceof Mage_Adminhtml_Block_Widget_Grid_Column
                && (!empty($data[$columnId]) || strlen($data[$columnId]) > 0)
                && $column->getFilter()
            ) {
                $column->getFilter()->setValue($data[$columnId]);
                $this->_addColumnFilterToCollection($column);
            }
        }

        return $this;
    }

    /**
     * Add filter
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = $column->getFilterIndex() ?: $column->getIndex();
            if ($column->getFilterConditionCallback() && $column->getFilterConditionCallback()[0] instanceof self) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();
                if ($field && $cond !== null) {
                    $filtered = array_map(static function ($value) {
                        return is_object($value) ? $value->__toString() : $value;
                    }, is_array($cond) ? array_values($cond) : [$cond]);
                    if (in_array('\'%NULL%\'', $filtered, true) || in_array('NULL', $filtered, true)) {
                        $this->getCollection()->addFieldToFilter($field, ['null' => true]);
                    } else {
                        $this->getCollection()->addFieldToFilter($field, $cond);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Add link model filter from grid column to collection
     *
     * @param Mage_Catalog_Model_Resource_Product_Link_Product_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     *
     * @return $this
     */
    protected function _addLinkModelFilterCallback($collection, $column)
    {
        $field = $column->getFilterIndex() ?: $column->getIndex();
        $condition = $column->getFilter()->getCondition();
        $collection->addLinkModelFieldToFilter($field, $condition);

        return $this;
    }

    /**
     * Sets sorting order by some column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?: $column->getIndex();
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));
        }
        return $this;
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()) {
            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                /** @var Mage_Adminhtml_Helper_Data $helper */
                $helper = $this->helper('adminhtml');
                $data = $helper->prepareFilterString($filter);
                $this->_setFilterValues($data);
            } elseif ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            } elseif (count($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir) === 'desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->_beforeLoadCollection();
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

    /**
     * Decode URL encoded filter value recursive callback method
     *
     * @param string $value
     */
    protected function _decodeFilter(&$value)
    {
        /** @var Mage_Adminhtml_Helper_Data $helper */
        $helper = $this->helper('adminhtml');
        $value = $helper->decodeFilter($value);
    }

    protected function _preparePage()
    {
        $this->getCollection()->setPageSize((int) $this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
        $this->getCollection()->setCurPage((int) $this->getParam($this->getVarNamePage(), $this->_defaultPage));
    }

    /**
     * Prepare columns for grid
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->sortColumnsByOrder();
        return $this;
    }

    /**
     * Prepare grid massaction block
     *
     * @return $this
     */
    protected function _prepareMassactionBlock()
    {
        $this->setChild('massaction', $this->getLayout()->createBlock($this->getMassactionBlockName()));
        $this->_prepareMassaction();
        if ($this->getMassactionBlock()->isAvailable()) {
            $this->_prepareMassactionColumn();
        }
        return $this;
    }

    /**
     * Prepare grid massaction actions
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Prepare grid massaction column
     *
     * @return $this
     */
    protected function _prepareMassactionColumn()
    {
        $columnId = 'massaction';
        $massactionColumn = $this->getLayout()->createBlock('adminhtml/widget_grid_column')
                ->setData([
                    'index'        => $this->getMassactionIdField(),
                    'filter_index' => $this->getMassactionIdFilter(),
                    'type'         => 'massaction',
                    'name'         => $this->getMassactionBlock()->getFormFieldName(),
                    'align'        => 'center',
                    'is_system'    => true,
                ]);

        if ($this->getNoFilterMassactionColumn()) {
            $massactionColumn->setData('filter', false);
        }

        $massactionColumn->setSelected($this->getMassactionBlock()->getSelected())
            ->setGrid($this)
            ->setId($columnId);

        $oldColumns = $this->_columns;
        $this->_columns = [];
        $this->_columns[$columnId] = $massactionColumn;
        $this->_columns = array_merge($this->_columns, $oldColumns);
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareGrid()
    {
        $this->_prepareColumns();
        $this->_prepareMassactionBlock();
        $this->_prepareCollection();
        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        try {
            $this->_prepareGrid();
        } catch (Exception $e) {
            $this->resetSavedParametersInSession();
            throw $e;
        }

        return parent::_beforeToHtml();
    }

    /**
     * @return $this
     */
    protected function _afterLoadCollection()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _beforeLoadCollection()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getVarNameLimit()
    {
        return $this->_varNameLimit;
    }

    /**
     * @return string
     */
    public function getVarNamePage()
    {
        return $this->_varNamePage;
    }

    /**
     * @return string
     */
    public function getVarNameSort()
    {
        return $this->_varNameSort;
    }

    /**
     * @return string
     */
    public function getVarNameDir()
    {
        return $this->_varNameDir;
    }

    /**
     * @return string
     */
    public function getVarNameFilter()
    {
        return $this->_varNameFilter;
    }

    /**
     * @param string $name
     * @return string
     */
    public function setVarNameLimit($name)
    {
        return $this->_varNameLimit = $name;
    }

    /**
     * @param string $name
     * @return string
     */
    public function setVarNamePage($name)
    {
        return $this->_varNamePage = $name;
    }

    /**
     * @param string $name
     * @return string
     */
    public function setVarNameSort($name)
    {
        return $this->_varNameSort = $name;
    }

    /**
     * @param string $name
     * @return string
     */
    public function setVarNameDir($name)
    {
        return $this->_varNameDir = $name;
    }

    /**
     * @param string $name
     * @return string
     */
    public function setVarNameFilter($name)
    {
        return $this->_varNameFilter = $name;
    }

    /**
     * Set visibility of column headers
     *
     * @param bool $visible
     */
    public function setHeadersVisibility($visible = true)
    {
        $this->_headersVisibility = $visible;
    }

    /**
     * Return visibility of column headers
     *
     * @return bool
     */
    public function getHeadersVisibility()
    {
        return $this->_headersVisibility;
    }

    /**
     * Set visibility of pager
     *
     * @param bool $visible
     */
    public function setPagerVisibility($visible = true)
    {
        $this->_pagerVisibility = $visible;
    }

    /**
     * Return visibility of pager
     *
     * @return bool
     */
    public function getPagerVisibility()
    {
        return $this->_pagerVisibility;
    }

    /**
     * Set visibility of filter
     *
     * @param bool $visible
     */
    public function setFilterVisibility($visible = true)
    {
        $this->_filterVisibility = $visible;
    }

    /**
     * Return visibility of filter
     *
     * @return bool
     */
    public function getFilterVisibility()
    {
        return $this->_filterVisibility;
    }

    /**
     * Set visibility of filter
     *
     * @param bool $visible
     */
    public function setMessageBlockVisibility($visible = true)
    {
        $this->_messageBlockVisibility = $visible;
    }

    /**
     * Return visibility of filter
     *
     * @return bool
     */
    public function getMessageBlockVisibility()
    {
        return $this->_messageBlockVisibility;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setDefaultLimit($limit)
    {
        $this->_defaultLimit = $limit;
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setDefaultPage($page)
    {
        $this->_defaultPage = $page;
        return $this;
    }

    /**
     * @param string $sort
     * @return $this
     */
    public function setDefaultSort($sort)
    {
        $this->_defaultSort = $sort;
        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setDefaultDir($dir)
    {
        $this->_defaultDir = $dir;
        return $this;
    }

    /**
     * @param array $filter
     * @return $this
     */
    public function setDefaultFilter($filter)
    {
        $this->_defaultFilter = $filter;
        return $this;
    }

    /**
     * Retrieve grid export types
     *
     * @return array|false
     */
    public function getExportTypes()
    {
        return empty($this->_exportTypes) ? false : $this->_exportTypes;
    }

    /**
     * Add new export type to grid
     *
     * @param   string $url
     * @param   string $label
     * @return  $this
     */
    public function addExportType($url, $label)
    {
        $this->_exportTypes[] = new Varien_Object(
            [
                'url'   => $this->getUrl($url, ['_current' => true]),
                'label' => $label,
            ],
        );
        return $this;
    }

    /**
    * Retrieve rss lists types
    *
    * @return array|false
    */
    public function getRssLists()
    {
        return empty($this->_rssLists) ? false : $this->_rssLists;
    }

    /**
     * Returns url for RSS
     * Can be overloaded in descendant classes to perform custom changes to url passed to addRssList()
     *
     * @param string $url
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _getRssUrl($url)
    {
        $urlModel = Mage::getModel('core/url');
        if (Mage::app()->getStore()->getStoreInUrl()) {
            // Url in 'admin' store view won't be accessible, so form it in default store view frontend
            $urlModel->setStore(Mage::app()->getDefaultStoreView());
        }
        return $urlModel->getUrl($url);
    }

    /**
     * Add new rss list to grid
     *
     * @param string $url
     * @param string $label
     * @return  $this
     * @throws Mage_Core_Model_Store_Exception
     */
    public function addRssList($url, $label)
    {
        $this->_rssLists[] = new Varien_Object(
            [
                'url'   => $this->_getRssUrl($url),
                'label' => $label,
            ],
        );
        return $this;
    }

    /**
     * Retrieve grid HTML
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->toHtml();
    }

    /**
     * Retrieve file content from file container array
     *
     * @return string
     */
    protected function _getFileContainerContent(array $fileData)
    {
        $io = new Varien_Io_File();
        $path = $io->dirname($fileData['value']);
        $io->open(['path' => $path]);
        return $io->read($fileData['value']);
    }

    /**
     * Retrieve Headers row array for Export
     *
     * @return array
     */
    protected function _getExportHeaders()
    {
        $row = [];
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getExportHeader();
            }
        }
        return $row;
    }

    /**
     * Retrieve Totals row array for Export
     *
     * @return array
     */
    protected function _getExportTotals()
    {
        $totals = $this->getTotals();
        $row    = [];
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = ($column->hasTotalsLabel()) ? $column->getTotalsLabel() : $column->getRowFieldExport($totals);
            }
        }
        return $row;
    }

    /**
     * Iterate collection and call callback method per item
     * For callback method first argument always is item object
     *
     * @param string $callback
     * @param array $args additional arguments for callback method
     */
    public function _exportIterateCollection($callback, array $args)
    {
        $originalCollection = $this->getCollection();
        $count = null;
        $page  = 1;
        $lPage = null;
        $break = false;

        while ($break !== true) {
            $collection = clone $originalCollection;
            $collection->setPageSize($this->_exportPageSize);
            $collection->setCurPage($page);
            // phpcs:ignore Ecg.Performance.Loop.ModelLSD
            $collection->load();
            if (is_null($count)) {
                $count = $collection->getSize();
                $lPage = $collection->getLastPageNumber();
            }
            if ($lPage == $page) {
                $break = true;
            }
            $page++;

            foreach ($collection as $item) {
                call_user_func_array([$this, $callback], array_merge([$item], $args));
            }
            $collection->clear();
            unset($collection);
        }
    }

    /**
     * Write item data to csv export file
     */
    protected function _exportCsvItem(Varien_Object $item, Varien_Io_File $adapter)
    {
        $row = [];
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($item);
            }
        }

        $adapter->streamWriteCsv(
            Mage::helper('core')->getEscapedCSVData($row),
        );
    }

    /**
     * Retrieve a file container array by grid data as CSV
     *
     * Return array with keys type and value
     *
     * @return array
     * @throws Exception
     */
    public function getCsvFile()
    {
        $this->_isExport = true;
        $this->_prepareGrid();

        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.csv';

        $io->setAllowCreateFolders(true);
        $io->open(['path' => $path]);
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWriteCsv($this->_getExportHeaders());

        $this->_exportIterateCollection('_exportCsvItem', [$io]);

        if ($this->getCountTotals()) {
            $io->streamWriteCsv(
                Mage::helper('core')->getEscapedCSVData($this->_getExportTotals()),
            );
        }

        $io->streamUnlock();
        $io->streamClose();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true, // can delete file after use
        ];
    }

    /**
     * Retrieve Grid data as CSV
     *
     * @return string
     * @throws Exception
     */
    public function getCsv()
    {
        $csv = '';
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->_beforeLoadCollection();
        $this->getCollection()->load();
        $this->_afterLoadCollection();

        $data = [];
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $data[] = '"' . $column->getExportHeader() . '"';
            }
        }
        $csv .= implode(',', $data) . "\n";

        foreach ($this->getCollection() as $item) {
            $data = [];
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(
                        ['"', '\\'],
                        ['""', '\\\\'],
                        $column->getRowFieldExport($item),
                    ) . '"';
                }
            }
            $csv .= implode(',', $data) . "\n";
        }

        if ($this->getCountTotals()) {
            $data = [];
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $data[] = '"' . str_replace(
                        ['"', '\\'],
                        ['""', '\\\\'],
                        $column->getRowFieldExport($this->getTotals()),
                    ) . '"';
                }
            }
            $csv .= implode(',', $data) . "\n";
        }

        return $csv;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getXml()
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->_beforeLoadCollection();
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $indexes = [];
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $indexes[] = $column->getIndex();
            }
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<items>';
        foreach ($this->getCollection() as $item) {
            $xml .= $item->toXml($indexes);
        }
        if ($this->getCountTotals()) {
            $xml .= $this->getTotals()->toXml($indexes);
        }
        return $xml . '</items>';
    }

    /**
     * Write item data to Excel 2003 XML export file
     *
     * @param Varien_Convert_Parser_Xml_Excel $parser
     */
    protected function _exportExcelItem(Varien_Object $item, Varien_Io_File $adapter, $parser = null)
    {
        if (is_null($parser)) {
            $parser = new Varien_Convert_Parser_Xml_Excel();
        }

        $row = [];
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $row[] = $column->getRowFieldExport($item);
            }
        }
        $data = $parser->getRowXml($row);
        $adapter->streamWrite($data);
    }

    /**
     * Retrieve a file container array by grid data as MS Excel 2003 XML Document
     *
     * Return array with keys type and value
     *
     * @param string $sheetName
     * @return array
     * @throws Exception
     */
    public function getExcelFile($sheetName = '')
    {
        $this->_isExport = true;
        $this->_prepareGrid();

        $parser = new Varien_Convert_Parser_Xml_Excel();
        $io     = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = md5(microtime());
        $file = $path . DS . $name . '.xml';

        $io->setAllowCreateFolders(true);
        $io->open(['path' => $path]);
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);
        $io->streamWrite($parser->getHeaderXml($sheetName));
        $io->streamWrite($parser->getRowXml($this->_getExportHeaders()));

        $this->_exportIterateCollection('_exportExcelItem', [$io, $parser]);

        if ($this->getCountTotals()) {
            $io->streamWrite($parser->getRowXml($this->_getExportTotals()));
        }

        $io->streamWrite($parser->getFooterXml());
        $io->streamUnlock();
        $io->streamClose();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true, // can delete file after use
        ];
    }

    /**
     * Retrieve grid data as MS Excel 2003 XML Document
     *
     * @param string $filename the Workbook sheet name
     * @return string
     * @throws Exception
     */
    public function getExcel($filename = '')
    {
        $this->_isExport = true;
        $this->_prepareGrid();
        $this->getCollection()->getSelect()->limit();
        $this->getCollection()->setPageSize(0);
        $this->_beforeLoadCollection();
        $this->getCollection()->load();
        $this->_afterLoadCollection();
        $headers = [];
        $data = [];
        foreach ($this->_columns as $column) {
            if (!$column->getIsSystem()) {
                $headers[] = $column->getHeader();
            }
        }
        $data[] = $headers;

        foreach ($this->getCollection() as $item) {
            $row = [];
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($item);
                }
            }
            $data[] = $row;
        }

        if ($this->getCountTotals()) {
            $row = [];
            foreach ($this->_columns as $column) {
                if (!$column->getIsSystem()) {
                    $row[] = $column->getRowField($this->getTotals());
                }
            }
            $data[] = $row;
        }

        $xmlObj = new Varien_Convert_Parser_Xml_Excel();
        $xmlObj->setVar('single_sheet', $filename);
        $xmlObj->setData($data);
        $xmlObj->unparse();

        return $xmlObj->getData();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function canDisplayContainer()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            return false;
        }
        return true;
    }

    /**
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getCurrentUrl();
    }

    /**
     * Grid url getter
     * Version of getGridUrl() but with parameters
     *
     * @param array $params url parameters
     * @return string current grid url
     */
    public function getAbsoluteGridUrl($params = [])
    {
        return $this->getCurrentUrl($params);
    }

    /**
     * Retrieve grid
     *
     * @param string $paramName
     * @param mixed $default
     * @return mixed
     * @throws Exception
     */
    public function getParam($paramName, $default = null)
    {
        $session = Mage::getSingleton('adminhtml/session');
        $sessionParamName = $this->getId() . $paramName;
        if ($this->getRequest()->has($paramName)) {
            $param = $this->getRequest()->getParam($paramName);
            if ($this->_saveParametersInSession) {
                $session->setData($sessionParamName, $param);
            }
            return $param;
        }

        if ($this->_saveParametersInSession && ($param = $session->getData($sessionParamName))) {
            return $param;
        }

        return $default;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setSaveParametersInSession($flag)
    {
        $this->_saveParametersInSession = $flag;
        return $this;
    }

    public function resetSavedParametersInSession()
    {
        $session = Mage::getSingleton('adminhtml/session');

        $params = [
            $this->_varNameLimit,
            $this->_varNamePage,
            $this->_varNameSort,
            $this->_varNameDir,
            $this->_varNameFilter,
        ];

        foreach ($params as $param) {
            $session->unsetData($this->getId() . $param);
        }
    }

    /**
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getId() . 'JsObject';
    }

    /**
     * @param Varien_Object $row
     * @return string
     * @deprecated since 1.1.7
     *
     */
    public function getRowId($row)
    {
        return $this->getRowUrl($row);
    }

    /**
     * Retrieve massaction row identifier field
     *
     * @return string
     */
    public function getMassactionIdField()
    {
        return $this->_massactionIdField;
    }

    /**
     * Set massaction row identifier field
     *
     * @param  string    $idField
     * @return $this
     */
    public function setMassactionIdField($idField)
    {
        $this->_massactionIdField = $idField;
        return $this;
    }

    /**
     * Retrieve massaction row identifier filter
     *
     * @return string
     */
    public function getMassactionIdFilter()
    {
        return $this->_massactionIdFilter;
    }

    /**
     * Set massaction row identifier filter
     *
     * @param string $idFilter
     * @return $this
     */
    public function setMassactionIdFilter($idFilter)
    {
        $this->_massactionIdFilter = $idFilter;
        return $this;
    }

    /**
     * Retrieve massaction block name
     *
     * @return string
     */
    public function getMassactionBlockName()
    {
        return $this->_massactionBlockName;
    }

    /**
     * Set massaction block name
     *
     * @param  string    $blockName
     * @return $this
     */
    public function setMassactionBlockName($blockName)
    {
        $this->_massactionBlockName = $blockName;
        return $this;
    }

    /**
     * Retrieve massaction block
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Massaction_Abstract
     */
    public function getMassactionBlock()
    {
        return $this->getChild('massaction');
    }

    /**
     * @return string
     */
    public function getMassactionBlockHtml()
    {
        return $this->getChildHtml('massaction');
    }

    /**
     * Set empty text for grid
     *
     * @param string $text
     * @return $this
     */
    public function setEmptyText($text)
    {
        $this->_emptyText = $text;
        return $this;
    }

    /**
     * Return empty text for grid
     *
     * @return string
     */
    public function getEmptyText()
    {
        return $this->_emptyText;
    }

    /**
     * Set empty text CSS class
     *
     * @param string $cssClass
     * @return $this
     */
    public function setEmptyTextClass($cssClass)
    {
        $this->_emptyTextCss = $cssClass;
        return $this;
    }

    /**
     * Return empty text CSS class
     *
     * @return string
     */
    public function getEmptyTextClass()
    {
        return $this->_emptyTextCss;
    }

    /**
     * Set count totals
     *
     * @param bool $count
     */
    public function setCountTotals($count = true)
    {
        $this->_countTotals = $count;
    }

    /**
     * Return count totals
     *
     * @return bool
     */
    public function getCountTotals()
    {
        return $this->_countTotals;
    }

    /**
     * Set totals
     */
    public function setTotals(Varien_Object $totals)
    {
        $this->_varTotals = $totals;
    }

    /**
     * Retrieve totals
     *
     * @return Varien_Object
     */
    public function getTotals()
    {
        return $this->_varTotals;
    }

    /**
     * Set subtotals
     *
     * @param bool $flag
     * @return $this
     */
    public function setCountSubTotals($flag = true)
    {
        $this->_countSubTotals = $flag;
        return $this;
    }

    /**
     * Return count subtotals
     *
     * @return bool
     */
    public function getCountSubTotals()
    {
        return $this->_countSubTotals;
    }

    /**
     * Set subtotal items
     *
     * @return $this
     */
    public function setSubTotals(array $items)
    {
        $this->_subtotals = $items;
        return $this;
    }

    /**
     * Retrieve subtotal item
     *
     * @param Varien_Object $item
     * @return Varien_Object|string
     */
    public function getSubTotalItem($item)
    {
        foreach ($this->_subtotals as $subtotalItem) {
            foreach ($this->_groupedColumn as $groupedColumn) {
                if ($subtotalItem->getData($groupedColumn) == $item->getData($groupedColumn)) {
                    return $subtotalItem;
                }
            }
        }
        return '';
    }

    /**
     * Retrieve subtotal items
     *
     * @return array
     */
    public function getSubTotals()
    {
        return $this->_subtotals;
    }

    /**
     * Check whether subtotal should be rendered
     *
     * @param Varien_Object $item
     * @return bool
     */
    public function shouldRenderSubTotal($item)
    {
        return ($this->_countSubTotals && count($this->_subtotals) > 0 && count($this->getMultipleRows($item)) > 0);
    }

    /**
     * Retrieve columns to render
     *
     * @return Mage_Adminhtml_Block_Widget_Grid_Column[]
     */
    public function getSubTotalColumns()
    {
        return $this->getColumns();
    }

    /**
     * Retrieve rowspan number
     *
     * @param Varien_Object $item
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return int|bool
     */
    public function getRowspan($item, $column)
    {
        if ($this->isColumnGrouped($column)) {
            return count($this->getMultipleRows($item)) + count($this->_groupedColumn);
        }
        return false;
    }

    /**
     * @param string|object $column
     * @param string $value
     * @return bool|$this
     */
    public function isColumnGrouped($column, $value = null)
    {
        if ($value === null) {
            if (is_object($column)) {
                return in_array($column->getIndex(), $this->_groupedColumn);
            }
            return in_array($column, $this->_groupedColumn);
        }
        $this->_groupedColumn[] = $column;
        return $this;
    }

    /**
     * Get children of specified item
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        return $item->getChildren();
    }

    /**
     * Retrieve columns for multiple rows
     *
     * @return array
     */
    public function getMultipleRowColumns()
    {
        $columns = $this->getColumns();
        foreach ($this->_groupedColumn as $column) {
            unset($columns[$column]);
        }
        return $columns;
    }

    /**
     * Check whether should render cell
     *
     * @param Varien_Object $item
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return bool
     */
    public function shouldRenderCell($item, $column)
    {
        if ($this->isColumnGrouped($column) && $item->getIsEmpty()) {
            return true;
        }
        if (!$item->getIsEmpty()) {
            return true;
        }
        return false;
    }

    /**
     * Check whether should render empty cell
     *
     * @param Varien_Object $item
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return bool
     */
    public function shouldRenderEmptyCell($item, $column)
    {
        return ($item->getIsEmpty() && in_array($column['index'], $this->_groupedColumn));
    }

    /**
     * Retrieve colspan for empty cell
     *
     * @return int
     */
    public function getEmptyCellColspan()
    {
        return $this->getColumnCount() - count($this->_groupedColumn);
    }

    /**
     * Retrieve label for empty cell
     *
     * @return string
     */
    public function getEmptyCellLabel()
    {
        return $this->_emptyCellLabel;
    }

    /**
     * Set label for empty cell
     *
     * @param string $label
     * @return $this
     */
    public function setEmptyCellLabel($label)
    {
        $this->_emptyCellLabel = $label;
        return $this;
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $res = parent::getRowUrl($row);
        return $res ?: '#';
    }

    /**
     * @return int[]
     */
    public function getLimitOptions(): array
    {
        return [20, 30, 50, 100, 200, 500, 1000];
    }
}
