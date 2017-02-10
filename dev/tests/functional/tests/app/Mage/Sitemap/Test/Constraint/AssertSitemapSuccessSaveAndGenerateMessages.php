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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Sitemap\Test\Constraint;

use Mage\Sitemap\Test\Fixture\Sitemap;
use Mage\Sitemap\Test\Page\Adminhtml\SitemapIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that success messages are displayed after sitemap generate.
 */
class AssertSitemapSuccessSaveAndGenerateMessages extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

    /**
     * Success sitemap generate message.
     */
    const SUCCESS_GENERATE_MESSAGE = 'The sitemap "%s" has been generated.';

    /**
     * Success sitemap save message.
     */
    const SUCCESS_SAVE_MESSAGE = 'The sitemap has been saved.';

    /**
     * Assert that success messages are displayed after sitemap generate.
     *
     * @param SitemapIndex $sitemapIndex
     * @param Sitemap $sitemap
     * @return void
     */
    public function processAssert(SitemapIndex $sitemapIndex, Sitemap $sitemap)
    {
        $actualMessages = $sitemapIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array(self::SUCCESS_SAVE_MESSAGE, $actualMessages) &&
            in_array(sprintf(self::SUCCESS_GENERATE_MESSAGE, $sitemap->getSitemapFilename()), $actualMessages),
            "The messages about the successful sitemap creation and generation are not present."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sitemap success generate and save messages are present.';
    }
}
