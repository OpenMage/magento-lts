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

namespace Mage\Adminhtml\Test\Block\Cms\Page\Edit;

use Mage\Adminhtml\Test\Block\Widget\FormTabs;
use Mage\Cms\Test\Fixture\CmsPage;
use Magento\Mtf\Client\Element\SimpleElement as Element;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\FixtureInterface;

/**
 * Backend Cms Page edit page.
 */
class PageForm extends FormTabs
{
    /**
     * Selector for store view field.
     *
     * @var string
     */
    protected $storeView = '#page_store_id';

    /**
     * Fill form with tabs.
     *
     * @param FixtureInterface $cms
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $cms, Element $element = null)
    {
        $this->fillStoreView($cms);
        return parent::fill($cms, $element);
    }

    /**
     * Fill store view.
     *
     * @param CmsPage $cms
     * @return void
     */
    protected function fillStoreView(CmsPage $cms)
    {
        $this->openTab('page_information');
        $storeViewField = $this->_rootElement->find($this->storeView, Locator::SELECTOR_CSS, 'multiselectgrouplist');
        if($storeViewField->isVisible() && !$cms->hasData('store_id')) {
            $storeViewField->setValue('All Store Views');
        }
    }
}
