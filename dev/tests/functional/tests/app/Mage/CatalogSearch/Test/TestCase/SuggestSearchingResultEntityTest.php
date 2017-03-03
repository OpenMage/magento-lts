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
