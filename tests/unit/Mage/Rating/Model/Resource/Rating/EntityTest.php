<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rating\Model\Resource\Rating;

use Mage_Rating_Model_Resource_Rating_Entity as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Rating\Model\Resource\Rating\EntityTrait;

final class EntityTest extends OpenMageTest
{
    use EntityTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
