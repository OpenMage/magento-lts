<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Category\Edit;

use Mage\Catalog\Test\Fixture\CatalogCategory;
use Magento\Mtf\Client\Locator;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Widget\FormTabs;

/**
 * Category form.
 */
class CategoryForm extends FormTabs
{
    /**
     * Save button.
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id=category-edit-form-save-button]';

    /**
     * Get data of Category information.
     *
     * @param CatalogCategory $category
     * @return array
     */
    public function getDataCategory(CatalogCategory $category)
    {
        return $category->hasData() ? parent::getData($category) : parent::getData();
    }

    /**
     * Open tab.
     *
     * @param string $tabName
     * @return Tab
     */
    public function openTab($tabName)
    {
        $selector = $this->tabs[$tabName]['selector'];
        $strategy = isset($this->tabs[$tabName]['strategy'])
            ? $this->tabs[$tabName]['strategy']
            : Locator::SELECTOR_CSS;
        $tab = $this->_rootElement->find($selector, $strategy);
        $this->browser->find('#global_search')->click();
        $tab->click();

        return $this;
    }
}
