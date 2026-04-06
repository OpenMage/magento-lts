<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogRule\Model\Action\Index\Refresh;

use Mage;
use Mage_CatalogRule_Model_Action_Index_Refresh_Row as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogRule\Model\Action\Index\Refresh\RowTrait;

final class RowTest extends OpenMageTest
{
    use RowTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalogrule/action_index_refresh_row');
    }
}
