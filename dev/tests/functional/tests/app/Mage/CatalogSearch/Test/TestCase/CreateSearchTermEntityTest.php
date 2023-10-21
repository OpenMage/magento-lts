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
