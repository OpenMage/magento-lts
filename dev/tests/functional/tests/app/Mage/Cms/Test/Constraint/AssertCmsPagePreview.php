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

namespace Mage\Cms\Test\Constraint;

use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Browser;
use Mage\Cms\Test\Fixture\CmsPage;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;
use Mage\Cms\Test\Page\CmsPage as FrontendCmsPage;

/**
 * Assert that content of created cms page displayed in main content section and equals passed from fixture.
 */
class AssertCmsPagePreview extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * I-frame selector.
     *
     * @var string
     */
    protected $iFrameSelector = '#preview_iframe';

    /**
     * Loader selector.
     *
     * @var string
     */
    protected $loader = '#loading_mask_loader';

    /**
     * Assert that content of created cms page displayed in main content section and equals passed from fixture.
     *
     * @param CmsPage $cms
     * @param CmsPageIndex $cmsPageIndex
     * @param FrontendCmsPage $frontendCmsPage
     * @param Browser $browser
     * @param bool $isIFrame [optional]
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        CmsPageIndex $cmsPageIndex,
        FrontendCmsPage $frontendCmsPage,
        Browser $browser,
        $isIFrame = false
    ) {
        $cmsPageIndex->open();
        $cmsPageIndex->getCmsPageGridBlock()->searchAndReview(['title' => $cms->getTitle()]);
        $browser->selectWindow();
        if ($isIFrame) {
            $this->switchToFrame($browser);
        }
        $element = $browser->find('body');

        $fixtureContent = $cms->getContent();
        \PHPUnit_Framework_Assert::assertContains(
            $fixtureContent['content'],
            $frontendCmsPage->getCmsPageContentBlock()->getPageContent($element),
            'Wrong content is displayed.'
        );
        if ($cms->getContentHeading()) {
            \PHPUnit_Framework_Assert::assertEquals(
                strtolower($cms->getContentHeading()),
                strtolower($frontendCmsPage->getCmsPageContentBlock()->getPageTitle($element)),
                'Wrong title is displayed.'
            );
        }
        if (isset($fixtureContent['widget'])) {
            foreach ($fixtureContent['widget']['preset'] as $widget) {
                \PHPUnit_Framework_Assert::assertTrue(
                    $frontendCmsPage->getCmsPageContentBlock()->isWidgetVisible($widget),
                    "Widget '{$widget['widget_type']}' is not displayed."
                );
            }
        }
        $browser->closeWindow();
        $browser->selectWindow();
        $browser->switchToFrame();
    }

    /**
     * Switch to frame.
     *
     * @param Browser $browser
     * @return void
     */
    protected function switchToFrame(Browser $browser)
    {
        $selector = $this->loader;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                return $browser->find($selector)->isVisible() == false ? true : null;
            }
        );
        $browser->switchToFrame(new Locator($this->iFrameSelector));
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'CMS Page content equals to data from fixture.';
    }
}
