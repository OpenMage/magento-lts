<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\SalesRule\Model\Resource\Report\Rule;

use Mage_SalesRule_Model_Resource_Report_Rule_Updatedat as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\SalesRule\Model\Resource\Report\Rule\UpdatedatTrait;

final class UpdatedatTest extends OpenMageTest
{
    use UpdatedatTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
