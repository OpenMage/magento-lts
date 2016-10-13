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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Cms\Test\TestCase;

use Mage\Cms\Test\Fixture\CmsPage;
use Mage\Cms\Test\Fixture\CmsPageMultiStore;
use Mage\Cms\Test\Page\Adminhtml\CmsPageEdit;
use Mage\Cms\Test\Page\Adminhtml\CmsPageIndex;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\TestCase\Injectable;

/**
 * Preconditions:
 * 1. CMS Page is created
 *
 * Steps:
 * 1. Log in to Backend.
 * 2. Navigate to CMS > Pages > Manage Content.
 * 3. Select CMS Page from precondition.
 * 4. Edit CMS Page according to data set.
 * 5. Click 'Save Page'.
 * 6. Perform all assertions.
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MPERF-7512
 */
class UpdateCmsPageEntityTest extends Injectable
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
     * Fixture Factory.
     *
     * @var FixtureFactory
     */
    protected $factory;

    /**
     * Inject page.
     *
     * @param CmsPageIndex $cmsIndex
     * @param CmsPageEdit $cmsEdit
     * @param CmsPageMultiStore $cmsOriginal
     * @param FixtureFactory $factory
     * @return array
     */
    public function __inject(
        CmsPageIndex $cmsIndex,
        CmsPageEdit $cmsEdit,
        CmsPageMultiStore $cmsOriginal,
        FixtureFactory $factory
    ) {
        $cmsOriginal->persist();
        $this->cmsIndex = $cmsIndex;
        $this->cmsEdit = $cmsEdit;
        $this->factory = $factory;

        return ['cmsOriginal' => $cmsOriginal];
    }

    /**
     * Update CMS Page test.
     *
     * @param CmsPageMultiStore $cms
     * @param CmsPageMultiStore $cmsOriginal
     * @return array
     */
    public function test(CmsPageMultiStore $cms, CmsPageMultiStore $cmsOriginal)
    {
        // Steps
        $this->cmsIndex->open();
        $filter = ['title' => $cmsOriginal->getTitle()];
        $this->cmsIndex->getCmsPageGridBlock()->searchAndOpen($filter);
        $this->cmsEdit->getPageForm()->fill($cms);
        $this->cmsEdit->getPageMainActions()->save();

        return [
            'cms' => $this->factory->createByCode(
                    'cmsPage',
                    ['data' => array_merge($cmsOriginal->getData(), $cms->getData())]
                )
        ];
    }
}
