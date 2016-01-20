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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product;

use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Mage\Adminhtml\Test\Block\Widget\FormTabs;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;
use Mage\Catalog\Test\Fixture\CatalogCategory;
use Mage\Adminhtml\Test\Block\Widget\Tab;
use Mage\Adminhtml\Test\Block\Template;
use Mage\Catalog\Test\Fixture\ConfigurableProduct;
use Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\CustomAttribute;

/**
 * Product form on backend product page.
 */
class ProductForm extends FormTabs
{
    /**
     * Backend abstract block selector.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Attribute on the Product page.
     *
     * @var string
     */
    protected $attribute = './/td[1][contains(., "%s")]';

    /**
     * Attribute set on the Product page.
     *
     * @var string
     */
    protected $attributeSet = '//*[@class="content-header"]/h3[contains(text(),"%s")]';

    /**
     * Product Information locator on the Product page.
     *
     * @var string
     */
    protected $productInfo = '[id="page:left"] h3';

    /**
     * Websites tab selector.
     *
     * @var string
     */
    protected $websitesTab = '#product_info_tabs_websites';

    /**
     * Settings tab selector.
     *
     * @var string
     */
    protected $settingsTab = '#product_info_tabs_set';

    /**
     * Attribute block selector.
     *
     * @var string
     */
    protected $attributeBlock = './/tr[contains(., "%s")]';

    /**
     * Fill the product form.
     *
     * @param FixtureInterface $product
     * @param Element|null $element [optional]
     * @param CatalogCategory|null $category [optional]
     * @return FormTabs
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fill(FixtureInterface $product, Element $element = null, CatalogCategory $category = null)
    {
        $typeId = $this->getProductType($product);

        // Fill product type
        $this->fillSettingsTabs($product);

        if ($this->hasRender($typeId)) {
            $renderArguments = [
                'product' => $product,
                'element' => $element,
                'category' => $category,
            ];
            $this->callRender($typeId, 'fill', $renderArguments);
        } else {
            $this->fillDefaultFields($product, $element, $category);
        }

        return $this;
    }

    /**
     * Get data of the tabs.
     *
     * @param FixtureInterface|null $product
     * @param Element|null $element
     * @return array
     */
    public function getData(FixtureInterface $product = null, Element $element = null)
    {
        $data = parent::getData($product, $element);
        if ($this->isWebsiteTabVisible() && !isset($data['website_ids'])) {
            $data['website_ids'] = $this->getWebsiteTabData();
        }

        return $data;
    }

    /**
     * Get data from website tab.
     *
     * @return array
     */
    protected function getWebsiteTabData()
    {
        $this->openTab('websites');
        return $this->getTabElement('websites')->getData();
    }

    /**
     * Fill default fields.
     *
     * @param FixtureInterface $product
     * @param Element|null $element [optional]
     * @param CatalogCategory|null $category [optional]
     * @return void
     */
    protected function fillDefaultFields(
        FixtureInterface $product,
        Element $element = null,
        CatalogCategory $category = null
    ) {
        $tabs = $this->prepareTabs($product, $category);
        $this->fillTabs($tabs, $element);
        $this->fillWebsitesTab($product);
    }

    /**
     * Prepare tabs.
     *
     * @param FixtureInterface $product
     * @param CatalogCategory|null $category
     * @return array
     */
    protected function prepareTabs(FixtureInterface $product, CatalogCategory $category = null)
    {
        $tabs = $this->getFieldsByTabs($product);
        $categories = $this->prepareCategories($product, $category);
        if (!empty($categories)) {
            $tabs['categories'] = $categories;
        }
        return $tabs;
    }

    /**
     * Fill websites tab.
     *
     * @param FixtureInterface $product
     * @return void
     */
    protected function fillWebsitesTab(FixtureInterface $product)
    {
        if (!$this->isWebsiteTabVisible()) {
            return;
        }
        $data = $product->hasData('website_ids') ? $product->getWebsiteIds() : ['Main Website'];
        $this->openTab('websites');
        $this->getTabElement('websites')->fillFormTab($data);
    }

    /**
     * Check website tab visible.
     *
     * @return bool
     */
    protected function isWebsiteTabVisible()
    {
        return $this->_rootElement->find($this->websitesTab)->isVisible();
    }

    /**
     * Prepare categories for fill.
     *
     * @param FixtureInterface $product
     * @param CatalogCategory|null $category
     * @return array
     */
    protected function prepareCategories(FixtureInterface $product, CatalogCategory $category = null)
    {
        return $category
            ? [$category]
            : ($product->hasData('category_ids')
                ? $product->getDataFieldConfig('category_ids')['source']->getCategories()
                : []);
    }

    /**
     * Get product type.
     *
     * @param FixtureInterface $product
     * @return string|null
     */
    protected function getProductType(FixtureInterface $product)
    {
        $dataConfig = $product->getDataConfig();
        return isset($dataConfig['type_id']) ? $dataConfig['type_id'] : null;
    }

    /**
     * Fill product's settings.
     *
     * @param FixtureInterface $product
     * @return void
     */
    protected function fillSettingsTabs(FixtureInterface $product)
    {
        if ($this->_rootElement->find($this->settingsTab)->isVisible()) {
            $tabs = $this->prepareSettingsData($product);
            foreach ($tabs as $tabName => $tabFields) {
                $tabElement = $this->getTabElement($tabName);
                $tabElement->fillFormTab($tabFields);
            }
        }
    }

    /**
     * Prepare data for settings tabs.
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareSettingsData(FixtureInterface $product)
    {
        $typeId = $this->getProductType($product);
        $attributeSet = null;
        $tabs['settings']['product_type']['value'] = $this->hasRender($typeId)
            ? $this->callRender($typeId, 'convertProductType')
            : ucfirst($typeId) . ' Product';
        if ($product->hasData('attribute_set_id')) {
            $attributeSet = $product->getDataFieldConfig('attribute_set_id')['source']->getAttributeSet();
        } elseif ($product instanceof ConfigurableProduct && !$product->hasData('attribute_set_id')) {
            $attributeSet = $product->getDataFieldConfig('configurable_options')['source']->getAttributeSet();
        }
        if ($attributeSet) {
            $tabs['settings']['attribute_set_id']['value'] = $attributeSet->getAttributeSetName();
            if ($attributeSet->hasData('assigned_attributes')) {
                $attributes = $attributeSet->getDataFieldConfig('assigned_attributes')['source']->getAttributes();
                $tabs['super-settings']['attribute']['value'] = $attributes;
            }
        }

        return $tabs;
    }

    /**
     * Open product tab.
     *
     * @param string $tabName
     * @return Tab
     */
    public function openTab($tabName)
    {
        $this->_rootElement->find($this->productInfo)->click();
        parent::openTab($tabName);
        $this->getTemplateBlock()->waitLoader();

        return $this;
    }

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Check visibility of the attribute on the product page.
     *
     * @param mixed $productAttribute
     * @return bool
     */
    public function checkAttributeLabel($productAttribute)
    {
        $frontendLabel = (is_array($productAttribute))
            ? $productAttribute['frontend_label']
            : $productAttribute->getFrontendLabel();
        $attributeLabelLocator = sprintf($this->attribute, $frontendLabel);

        return $this->_rootElement->find($attributeLabelLocator, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Check attribute set name.
     *
     * @param string $name
     * @return bool
     */
    public function checkAttributeSet($name)
    {
        $attributeSetLabel = $this->_rootElement->find(sprintf($this->attributeSet, $name), Locator::SELECTOR_XPATH);
        $attributeSetLabel->click();
        return $attributeSetLabel->isVisible();
    }

    /**
     * Get require notice attributes.
     *
     * @param FixtureInterface $product
     * @return array
     */
    public function getRequireNoticeAttributes(FixtureInterface $product)
    {
        $data = [];
        $tabs = $this->getFieldsByTabs($product);
        foreach ($tabs as $tabName => $fields) {
            $tab = $this->getTabElement($tabName);
            $this->openTab($tabName);
            $errors = $tab->getRequireNoticeMessages();
            if (!empty($errors)) {
                $data[$tabName] = $errors;
            }
        }
        return $data;
    }

    /**
     * Get attribute element.
     *
     * @param CatalogProductAttribute $attribute
     * @return CustomAttribute
     */
    public function getAttributeElement(CatalogProductAttribute $attribute)
    {
        return $this->_rootElement->find(
            sprintf($this->attributeBlock, $attribute->getFrontendLabel()),
            Locator::SELECTOR_XPATH,
            'Mage\Adminhtml\Test\Block\Catalog\Product\Attribute\CustomAttribute'
        );
    }
}
