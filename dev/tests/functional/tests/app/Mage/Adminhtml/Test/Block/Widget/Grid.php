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
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Widget;

use Mage\Adminhtml\Test\Block\GridPageActions;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;

/**
 * Basic grid actions.
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class Grid extends GridPageActions
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Locator value for 'Search' button.
     *
     * @var string
     */
    protected $searchButton = '[title="Search"][class*=scalable]';

    /**
     * Locator for 'Sort' link.
     *
     * @var string
     */
    protected $sortLink = "[name='%s'][title='%s']";

    /**
     * Locator value for 'Reset' button.
     *
     * @var string
     */
    protected $resetButton = '[title="Reset Filter"][class*=scalable]';

    /**
     * The first row in grid. For this moment we suggest that we should strictly define what we are going to search.
     *
     * @var string
     */
    protected $rowItem = '.grid tbody tr';

    /**
     * Locator value for link in action column.
     *
     * @var string
     */
    protected $editLink = 'td.last a';

    /**
     * An element locator which allows to select entities in grid.
     *
     * @var string
     */
    protected $selectItem = 'tbody tr .checkbox';

    /**
     * 'Select All' link.
     *
     * @var string
     */
    protected $selectAll = '.massaction a[onclick*=".selectAll()"]';

    /**
     * Massaction dropdown.
     *
     * @var string
     */
    protected $massactionSelect = '[id*=massaction-select]';

    /**
     * Massaction dropdown.
     *
     * @var string
     */
    protected $massactionAction = '#massaction-select';

    /**
     * Massaction 'Submit' button.
     *
     * @var string
     */
    protected $massactionSubmit = '[id*=massaction-form] button';

    /**
     * Backend abstract block.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Selector of element to wait for. If set by child will wait for element after action.
     *
     * @var string
     */
    protected $waitForSelector;

    /**
     * Locator type of waitForSelector.
     *
     * @var Locator
     */
    protected $waitForSelectorType = Locator::SELECTOR_CSS;

    /**
     * Wait for should be for visibility or not.
     *
     * @var boolean
     */
    protected $waitForSelectorVisible = true;

    /**
     * Selector for action option select.
     *
     * @var string
     */
    protected $option = '[name="status"]';

    /**
     * Active class.
     *
     * @var string
     */
    protected $active = '.active';

    /**
     * Base part of row locator template for getRow() method.
     *
     * @var string
     */
    protected $location = '//div[@class="grid"]//tr[';

    /**
     * Secondary part of row locator template for getRow() method.
     *
     * @var string
     */
    protected $rowTemplate = 'td[contains(text(),normalize-space("%s"))]';

    /**
     * Secondary part of row locator template for getRow() method with strict option.
     *
     * @var string
     */
    protected $rowTemplateStrict = 'td[text()[normalize-space()="%s"]]';

    /**
     * Magento grid loader.
     *
     * @var string
     */
    protected $loader = '[data-role="spinner"]';

    /**
     * Locator for next page action.
     *
     * @var string
     */
    protected $actionNextPage = '.pager .action-next';

    /**
     * Locator for disabled next page action
     *
     * @var string
     */
    protected $actionNextPageDisabled = '.pager .action-next.disabled';

    /**
     * First row selector
     *
     * @var string
     */
    protected $firstRowSelector = '//tr[./td[contains(@class, "a-left")]][1]';

    /**
     * Body selector
     *
     * @var string
     */
    protected $body = 'body';

    /**
     * Column xpath pattern.
     *
     * @var string
     */
    private $columnSelector = '//table[%s]/tbody/tr/td[count(//table[%s]/thead/tr/th[%s]/preceding-sibling::th)+1]';

    /**
     * Products table identifier.
     *
     * @var string
     */
    protected $tableIdentifier = '';

    /**
     * An element locator which allows selected entities in grid.
     *
     * @var string
     */
    protected $selectedItem = '';

    /**
     * Get column data from specified filters.
     *
     * @param array $columnFilters
     * @return array
     */
    public function getColumnData(array $columnFilters)
    {
        $data = [];
        foreach ($columnFilters as $columnName => $selector) {
            $columnElements = $this->_rootElement
                ->getElements($this->prepareColumnSelector($selector), Locator::SELECTOR_XPATH);
            /** @var Element $columnElement */
            foreach ($columnElements as $columnElement) {
                $data[$columnName][] = $columnElement->getText();
            }
        }
        return $data;
    }

    /**
     * Prepare column xpath selector.
     *
     * @param $selector
     * @return string
     */
    protected function prepareColumnSelector($selector)
    {
        $tableIdentifier = isset($this->tableIdentifier) ? $this->tableIdentifier : '@*';
        return sprintf($this->columnSelector, $tableIdentifier, $tableIdentifier, $selector);
    }

    /**
     * Convert column data in accordance with rows in grid.
     * Example:
     * Columns data
     * 'id' => ['1', '2']
     * 'name' => ['Simple1', 'Simple2']
     *
     * converts to
     * 0 => ['id' = 1, 'name' => 'Simple1'];
     * 1 => ['id' = 2, 'name' => 'Simple2'];
     *
     * @param array $columnData
     * @return array
     */
    protected function rowsDataConverter(array $columnData)
    {
        $rowData = [];
        foreach ($columnData as $key => $dataValues) {
            foreach($dataValues as $index => $value){
                $rowData[$index][$key] = $value;
            }
        }

        return $rowData;
    }

    /**
     * Prepare data to perform search, fill in search filter.
     *
     * @param array $filters
     * @throws \Exception
     */
    protected function prepareForSearch(array $filters)
    {
        foreach ($filters as $key => $value) {
            if (isset($this->filters[$key])) {
                $selector = $this->filters[$key]['selector'];
                $strategy = isset($this->filters[$key]['strategy'])
                    ? $this->filters[$key]['strategy']
                    : Locator::SELECTOR_CSS;
                $typifiedElement = isset($this->filters[$key]['input'])
                    ? $this->filters[$key]['input']
                    : null;
                $this->_rootElement->find($selector, $strategy, $typifiedElement)->setValue($value);
            } else {
                throw new \Exception('Such column is absent in the grid or not described yet.');
            }
        }
    }

    /**
     * Search item via grid filter.
     *
     * @param array $filter
     */
    public function search(array $filter)
    {
        $this->resetFilter();
        $this->prepareForSearch($filter);
        $this->_rootElement->find($this->searchButton, Locator::SELECTOR_CSS)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Search item and open it.
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchAndOpen(array $filter)
    {
        $this->search($filter);
        $rowItem = $this->_rootElement->find($this->rowItem, Locator::SELECTOR_CSS);
        if ($rowItem->isVisible()) {
            $rowItem->find($this->editLink, Locator::SELECTOR_CSS)->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }

    /**
     * Search for item and select it.
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchAndSelect(array $filter)
    {
        $this->search($filter);
        $selectItem = $this->_rootElement->find($this->selectItem);
        if ($selectItem->isVisible()) {
            $selectItem->click();
        } else {
            throw new \Exception('Searched item was not found.');
        }
    }

    /**
     * Press 'Reset' button.
     */
    public function resetFilter()
    {
        $this->_rootElement->find($this->resetButton, Locator::SELECTOR_CSS)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Perform selected massaction over checked items.
     *
     * @param array $items
     * @param array|string $action [array -> key = value from first select; value => value from subselect]
     * @param bool $acceptAlert [optional]
     * @param string $massActionSelection [optional]
     * @return void
     */
    public function massaction(array $items, $action, $acceptAlert = false, $massActionSelection = '')
    {
        if (!is_array($action)) {
            $action = [$action => '-'];
        }
        foreach ($items as $item) {
            $this->searchAndSelect($item);
        }
        if ($massActionSelection) {
            $this->_rootElement->find($this->massactionAction, Locator::SELECTOR_CSS, 'select')
                ->setValue($massActionSelection);
        }
        $actionType = key($action);
        $this->_rootElement->find($this->massactionSelect, Locator::SELECTOR_CSS, 'select')->setValue($actionType);
        if (isset($action[$actionType]) && $action[$actionType] != '-') {
            $this->_rootElement->find($this->option, Locator::SELECTOR_CSS, 'select')->setValue($action[$actionType]);
        }
        $this->massActionSubmit($acceptAlert);
    }

    /**
     * Submit mass actions.
     *
     * @param bool $acceptAlert
     * @return void
     */
    protected function massActionSubmit($acceptAlert)
    {
        $this->_rootElement->find($this->massactionSubmit, Locator::SELECTOR_CSS)->click();
        if ($acceptAlert) {
            $this->browser->acceptAlert();
        }
    }

    /**
     * Obtain specific row in grid.
     *
     * @param array $filter
     * @param bool $isSearchable
     * @param bool $isStrict
     * @return Element
     */
    protected function getRow(array $filter, $isSearchable = true, $isStrict = true)
    {
        if ($isSearchable) {
            $this->search($filter);
        }
        $rowTemplate = 'td[contains(.,normalize-space("%s"))]';
        if ($isStrict) {
            $rowTemplate = 'td[text()[normalize-space()="%s"]]';
        }
        $rows = [];
        foreach ($filter as $value) {
            $rows[] = sprintf($rowTemplate, $value);
        }
        $location = $this->location . implode(' and ', $rows) . ']';
        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH);
    }

    /**
     * Get rows data.
     *
     * @param array $columns
     * @return array
     */
    public function getRowsData(array $columns)
    {
        $columnData = $this->getColumnData($columns);

        return $this->rowsDataConverter($columnData);
    }

    /**
     * Check if specific row exists in grid.
     *
     * @param array $filter
     * @param bool $isSearchable
     * @param bool $isStrict
     * @return bool
     */
    public function isRowVisible(array $filter, $isSearchable = true, $isStrict = true)
    {
        return $this->getRow($filter, $isSearchable, $isStrict)->isVisible();
    }

    /**
     * Sort grid by field.
     *
     * @param $field
     * @param string $sort
     */
    public function sortGridByField($field, $sort = "desc")
    {
        $sortBlock = $this->_rootElement->find(sprintf($this->sortLink, $field, $sort));
        if ($sortBlock->isVisible()) {
            $sortBlock->click();
            $this->getTemplateBlock()->waitLoader();
        }
    }

    /**
     * Click to next page action link.
     *
     * @return bool
     */
    protected function nextPage()
    {
        if ($this->_rootElement->find($this->actionNextPageDisabled)->isVisible()) {
            return false;
        }
        $this->_rootElement->find($this->actionNextPage)->click();
        $this->getTemplateBlock()->waitLoader();
        return true;
    }

    /**
     * Check whether first row is visible.
     *
     * @return bool
     */
    public function isFirstRowVisible()
    {
        return $this->_rootElement->find($this->firstRowSelector, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Open first item in grid.
     *
     * @return void
     */
    public function openFirstRow()
    {
        $this->_rootElement->find($this->firstRowSelector, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Check select entity.
     *
     * @param array $filter
     * @throws \Exception
     * @return bool
     */
    public function isSelect(array $filter)
    {
        try {
            $this->search($filter);
            $checkValue = $this->_rootElement->find($this->selectedItem, Locator::SELECTOR_CSS, 'checkbox')->getValue();
            if ($checkValue == 'Yes') {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            throw new \Exception("Searched item was not found. \n $e");
        }
    }
}
