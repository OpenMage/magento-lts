<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Directory\Model\Region\Api;

// use Mage;
// use Mage_Directory_Model_Region_Api_V2 as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Directory\Model\Region\Api\V2Trait;

final class V2Test extends OpenMageTest
{
    use V2Trait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('directory/region_api_v2');
        self::markTestSkipped('');
    }
}
