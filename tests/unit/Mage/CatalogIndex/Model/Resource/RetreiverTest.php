<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogIndex\Model\Resource;

// use Mage;
// use Mage_CatalogIndex_Model_Resource_Retreiver as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogIndex\Model\Resource\RetreiverTrait;

final class RetreiverTest extends OpenMageTest
{
    use RetreiverTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalogindex/resource_retreiver');
        self::markTestSkipped('');
    }
}
