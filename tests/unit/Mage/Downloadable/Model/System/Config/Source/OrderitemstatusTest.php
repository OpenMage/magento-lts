<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Downloadable\Model\System\Config\Source;

use Mage_Downloadable_Model_System_Config_Source_Orderitemstatus as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Downloadable\Model\System\Config\Source\OrderitemstatusTrait;

final class OrderitemstatusTest extends OpenMageTest
{
    use OrderitemstatusTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
