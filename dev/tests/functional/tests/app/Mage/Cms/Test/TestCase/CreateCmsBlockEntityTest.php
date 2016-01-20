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

use Mage\Cms\Test\Fixture\CmsBlock;
use Mage\Cms\Test\Page\Adminhtml\CmsBlockIndex;
use Mage\Cms\Test\Page\Adminhtml\CmsBlockNew;
use Magento\Mtf\TestCase\Injectable;

/**
 * Steps:
 * 1. Open Backend.
 * 2. Go to Cms > Static Blocks.
 * 3. Click "Add New Block" button.
 * 4. Fill data according to dataset.
 * 5. Perform all assertions.
 *
 * @group CMS_Content_(PS)
 * @ZephyrId MPERF-7430
 */
class CreateCmsBlockEntityTest extends Injectable
{
    /**
     * Page CmsBlockIndex.
     *
     * @var CmsBlockIndex
     */
    protected $cmsBlockIndex;

    /**
     * Page CmsBlockNew.
     *
     * @var CmsBlockNew
     */
    protected $cmsBlockNew;

    /**
     * Injection data.
     *
     * @param CmsBlockIndex $cmsBlockIndex
     * @param CmsBlockNew $cmsBlockNew
     * @return void
     */
    public function __inject(CmsBlockIndex $cmsBlockIndex, CmsBlockNew $cmsBlockNew)
    {
        $this->cmsBlockIndex = $cmsBlockIndex;
        $this->cmsBlockNew = $cmsBlockNew;
    }

    /**
     * Create CMS Block.
     *
     * @param CmsBlock $cmsBlock
     * @return void
     */
    public function test(CmsBlock $cmsBlock)
    {
        // Steps
        $this->cmsBlockIndex->open();
        $this->cmsBlockIndex->getGridPageActions()->addNew();
        $this->cmsBlockNew->getBlockForm()->fill($cmsBlock);
        $this->cmsBlockNew->getFormPageActions()->save();
    }
}
