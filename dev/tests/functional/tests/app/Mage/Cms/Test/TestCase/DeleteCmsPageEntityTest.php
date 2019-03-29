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

namespace Mage\Cms\Test\TestCase;

use Mage\Cms\Test\Fixture\CmsPage;
use Mage\Cms\Test\Fixture\CmsPageMultiStore;
use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;
use Mage\Cms\Test\Page\Adminhtml\CmsPageEdit;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. CMS Page is created.
 *
 * Steps:
 * 1. Log in to Backend.
 * 2. Navigate to CMS -> Pages -> Manage Content.
 * 3. Select CMS Page from precondition.
 * 4. Click "Delete Page" button.
 * 5. Perform all assertions.
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MPERF-7326
 */
class DeleteCmsPageEntityTest extends Injectable
{
    /**
     * CMS Index page.
     *
     * @var CmsPageIndex
     */
    protected $cmsIndex;

    /**
     * Edit CMS page.
     *
     * @var CmsPageEdit
     */
    protected $cmsEdit;

    /**
     * Inject pages.
     *
     * @param CmsPageIndex $cmsIndex
     * @param CmsPageEdit $cmsEdit
     * @return void
     */
    public function __inject(CmsPageIndex $cmsIndex, CmsPageEdit $cmsEdit)
    {
        $this->cmsIndex = $cmsIndex;
        $this->cmsEdit = $cmsEdit;
    }

    /**
     * Delete CMS Page.
     *
     * @param CmsPageMultiStore $cmsPage
     * @return void
     */
    public function test(CmsPageMultiStore $cmsPage)
    {
        // Preconditions
        $cmsPage->persist();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen(['title' => $cmsPage->getTitle()]);
        $this->cmsEdit->getPageMainActions()->deleteAndAcceptAlert();
    }
}
