<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Reports\Model;

use Mage_Reports_Model_Report as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Reports\Model\ReportTrait;

final class ReportTest extends OpenMageTest
{
    use ReportTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
