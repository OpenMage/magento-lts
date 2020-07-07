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

namespace Mage\Adminhtml\Test\Block\CatalogRule\Promo\Catalog\Edit;

use Mage\Adminhtml\Test\Block\Widget\FormTabs;
use Mage\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Form for creation of a Catalog Price Rule.
 */
class Form extends FormTabs
{
    /**
     * Selector for website field.
     *
     * @var string
     */
    protected $website = '#rule_website_ids';

    /**
     * Fill form with tabs.
     *
     * @param FixtureInterface $catalogPriceRule
     * @param Element $element [optional]
     * @param array $replace [optional]
     * @return void
     */
    public function fill(FixtureInterface $catalogPriceRule, Element $element = null, array $replace = null)
    {
        $tabs = $this->getFieldsByTabs($catalogPriceRule);
        if ($replace) {
            $tabs = $this->prepareData($tabs, $replace);
        }
        $this->fillWebsites($catalogPriceRule);
        $this->fillTabs($tabs, $element);
    }

    /**
     * Fill website.
     *
     * @param CatalogRule $catalogPriceRule
     * @return void
     */
    protected function fillWebsites(CatalogRule $catalogPriceRule)
    {
        $websiteField = $this->_rootElement->find($this->website, Locator::SELECTOR_CSS, 'multiselectlist');
        if ($websiteField->isVisible() && !$catalogPriceRule->hasData('website_ids')) {
            $websiteField->setValue('Main Website');
        }
    }

    /**
     * Replace placeholders in each values of data.
     *
     * @param array $tabs
     * @param array $replace
     * @return array
     */
    protected function prepareData(array $tabs, array $replace)
    {
        foreach ($replace as $tabName => $fields) {
            foreach ($fields as $key => $pairs) {
                if (isset($tabs[$tabName][$key])) {
                    $tabs[$tabName][$key]['value'] = str_replace(
                        array_keys($pairs),
                        array_values($pairs),
                        $tabs[$tabName][$key]['value']
                    );
                }
            }
        }

        return $tabs;
    }
}
