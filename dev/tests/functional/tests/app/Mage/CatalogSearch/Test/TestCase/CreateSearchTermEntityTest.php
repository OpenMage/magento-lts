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

namespace Mage\CatalogSearch\Test\TestCase;

use Mage\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Mage\CatalogSearch\Test\Page\Adminhtml\CatalogSearchEdit;
use Mage\CatalogSearch\Test\Page\Adminhtml\CatalogSearchIndex;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. Product is created.
 *
 * Steps:
 * 1. Go to backend as admin user.
 * 4. Navigate to Catalog -> Search Terms.
 * 5. Click "Add New Search Term" button.
 * 6. Fill out all data according to dataset.
 * 7. Save the Search Term.
 * 8. Perform all assertions.
 *
 * @group Search_Terms_(MX)
 * @ZephyrId MPERF-7591
 */
class CreateSearchTermEntityTest extends Injectable
{
    /**
     * Search term page.
     *
     * @var CatalogSearchIndex
     */
    protected $indexPage;

    /**
     * Search term edit page.
     *
     * @var CatalogSearchEdit
     */
    protected $editPage;

    /**
     * Inject pages.
     *
     * @param CatalogSearchIndex $indexPage
     * @param CatalogSearchEdit $editPage
     * @return void
     */
    public function __inject(CatalogSearchIndex $indexPage, CatalogSearchEdit $editPage)
    {
        $this->indexPage = $indexPage;
        $this->editPage = $editPage;
    }

    /**
     * Run create search term test.
     *
     * @param CatalogSearchQuery $searchTerm
     * @return void
     */
    public function test(CatalogSearchQuery $searchTerm)
    {
        // Steps
        $this->indexPage->open();
        $this->indexPage->getGridPageActions()->addNew();
        $this->editPage->getForm()->fill($searchTerm);
        $this->editPage->getFormPageActions()->save();
    }
}
