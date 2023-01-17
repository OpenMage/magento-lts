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
