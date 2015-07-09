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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Cms\Test\Constraint;

use Magento\Mtf\Client\Browser;
use Mage\Cms\Test\Fixture\CmsPage;
use Magento\Mtf\Constraint\AbstractConstraint;
use Mage\Cms\Test\Page\CmsPage as FrontendCmsPage;

/**
 * Assert that created CMS page with status "disabled" displays with error message on frontend.
 */
class AssertCmsPageDisabledOnFrontend extends AbstractConstraint
{
    /**
     * Text of error message.
     */
    const ERROR_MESSAGE = "The page you requested was not found, and we have a fine guess why.";

    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created CMS page with status "disabled" displays with error message on frontend.
     *
     * @param CmsPage $cms
     * @param FrontendCmsPage $frontendCmsPage
     * @param Browser $browser
     * @param string|null $notFoundMessage
     * @return void
     */
    public function processAssert(
        CmsPage $cms,
        FrontendCmsPage $frontendCmsPage,
        Browser $browser,
        $notFoundMessage = null
    ) {
        $notFoundMessage = ($notFoundMessage !== null) ? $notFoundMessage : self::ERROR_MESSAGE;
        $browser->open($_ENV['app_frontend_url'] . $cms->getIdentifier());
        \PHPUnit_Framework_Assert::assertContains(
            $notFoundMessage,
            $frontendCmsPage->getCmsPageContentBlock()->getPageContent(),
            'Wrong page is displayed.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Created CMS page with status "disabled" displays with error message on frontend.';
    }
}
