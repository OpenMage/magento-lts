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
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Adminhtml\Test\Fixture\Store;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Core\Test\Fixture\ConfigData;
use Mage\Adminhtml\Test\Page\Adminhtml\Cache;

/**
 * Assert that created store view can be localized.
 */
class AssertStoreLocalized extends AbstractConstraint
{
    /**
     * Account title in german localization.
     */
    const EXPECTED_TEXT = 'konto';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * CmsIndex page.
     *
     * @var CmsIndex
     */
    protected $cmsIndexPage;

    /**
     * Assert that created store view can be localized.
     *
     * @param Store $store
     * @param CmsIndex $cmsIndex
     * @param FixtureFactory $fixtureFactory
     * @param ConfigData $config
     * @param Cache $adminCache
     * @return void
     */
    public function processAssert(
        Store $store,
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory,
        ConfigData $config,
        Cache $adminCache
    ) {
        // Flush cache
        $adminCache->open();
        $adminCache->getPageActions()->flushCacheStorage();
        $adminCache->getMessagesBlock()->waitSuccessMessage();

        $this->cmsIndexPage = $cmsIndex;
        $this->setConfig($store, $fixtureFactory, $config);
        $cmsIndex->open();
        $this->selectStore($store);
        \PHPUnit_Framework_Assert::assertEquals(
            strtolower($cmsIndex->getTopLinksBlock()->getAccountLabelText()),
            self::EXPECTED_TEXT
        );
    }

    /**
     * Set config.
     *
     * @param Store $store
     * @param FixtureFactory $fixtureFactory
     * @param ConfigData $config
     * @return void
     */
    protected function setConfig(Store $store, FixtureFactory $fixtureFactory, ConfigData $config)
    {
        $configData = $config->getData();
        $configData['section']['general/locale/code']['scope'] .=  '/' . $store->getCode();
        $fixtureFactory->createByCode('configData', ['data' => $configData['section']])->persist();
    }

    /**
     * Select store.
     *
     * @param Store $store
     * @return void
     */
    protected function selectStore(Store $store)
    {
        $headerBlock = $this->cmsIndexPage->getHeaderBlock();
        $footerBlock = $this->cmsIndexPage->getFooterBlock();
        if ($footerBlock->isStoreGroupSwitcherVisible() && $footerBlock->isStoreGroupVisible($store)) {
            $footerBlock->selectStoreGroup($store);
        }
        if ($headerBlock->isStoreViewDropdownVisible()) {
            $headerBlock->selectStore($store->getName());
        };
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Created store view can be localized.';
    }
}
