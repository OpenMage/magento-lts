<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Sitemap\Edit;

use Mage\Sitemap\Test\Fixture\Sitemap;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Sitemap edit form.
 */
class Form extends \Magento\Mtf\Block\Form
{
    /**
     * Selector for store view field.
     *
     * @var string
     */
    protected $storeView = '#store_id';

    /**
     * Fill form.
     *
     * @param FixtureInterface $sitemap
     * @param SimpleElement|null $element
     * @return $this
     */
    public function fill(FixtureInterface $sitemap, SimpleElement $element = null)
    {
        $this->fillStoreView($sitemap);
        return parent::fill($sitemap, $element);
    }

    /**
     * Fill store view field.
     *
     * @param Sitemap $sitemap
     * @return void
     */
    protected function fillStoreView(Sitemap $sitemap)
    {
        $storeViewField = $this->_rootElement->find($this->storeView, Locator::SELECTOR_CSS, 'selectstore');
        if ($storeViewField->isVisible() && !$sitemap->hasData('store_id')) {
            $storeViewField->setValue('Main Website/Main Website Store/Default Store View');
        }
    }
}
