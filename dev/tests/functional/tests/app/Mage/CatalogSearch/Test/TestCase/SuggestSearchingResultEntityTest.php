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

namespace Mage\CatalogSearch\Test\TestCase;

use Mage\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Mage\Cms\Test\Page\CmsIndex;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Two simple products are created.
 *
 * Steps:
 * 1. Go to frontend on index page.
 * 2. Input in "Search" field test data.
 * 3. Perform asserts.
 *
 * @group Search_Frontend_(CS)
 * @ZephyrId MPERF-7584
 */
class SuggestSearchingResultEntityTest extends Injectable
{
    /**
     * Run suggest searching result test.
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogSearchQuery $catalogSearch
     * @return void
     */
    public function test(CmsIndex $cmsIndex, CatalogSearchQuery $catalogSearch)
    {
        // Steps:
        $cmsIndex->open();
        $cmsIndex->getSearchBlock()->search($catalogSearch->getQueryText());
    }
}
