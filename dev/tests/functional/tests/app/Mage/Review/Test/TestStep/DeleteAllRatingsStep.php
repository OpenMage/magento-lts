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

namespace Mage\Review\Test\TestStep;

use Mage\Rating\Test\Page\Adminhtml\RatingEdit;
use Mage\Rating\Test\Page\Adminhtml\RatingIndex;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Delete all ratings.
 */
class DeleteAllRatingsStep implements TestStepInterface
{
    /**
     * Backend rating grid page.
     *
     * @var RatingIndex
     */
    protected $ratingIndex;

    /**
     * Backend rating edit page.
     *
     * @var RatingEdit
     */
    protected $ratingEdit;

    /**
     * @constructor
     * @param RatingEdit $ratingEdit
     * @param RatingIndex $ratingIndex
     */
    public function __construct(RatingEdit $ratingEdit, RatingIndex $ratingIndex)
    {
        $this->ratingEdit = $ratingEdit;
        $this->ratingIndex = $ratingIndex;
    }

    /**
     * Delete all ratings.
     *
     * @return void
     */
    public function run()
    {
        $this->ratingIndex->open();
        while ($this->ratingIndex->getRatingGrid()->isFirstRowVisible()) {
            $this->ratingIndex->getRatingGrid()->openFirstRow();
            $this->ratingEdit->getPageActions()->deleteAndAcceptAlert();
        }
    }
}
