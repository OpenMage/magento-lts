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
