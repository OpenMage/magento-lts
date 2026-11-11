<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Model\Resource\Collection;

use Generator;
use Mage_Eav_Model_Entity_Attribute;

trait AbstractTrait
{
    /**
     * @return Generator<string, array{string, mixed}, mixed, void>
     */
    public static function provideAttributeToFieldData(): Generator
    {
        yield 'field name' => ['increment_id', 'increment_id'];
        yield 'attribute' => ['increment_id', new Mage_Eav_Model_Entity_Attribute(['attribute_code' => 'increment_id'])];
    }

    /**
     * @return Generator<string, array{string, mixed}, mixed, void>
     */
    public static function provideAttributeToFieldExceptionData(): Generator
    {
        yield 'empty string' => ["Cannot determine the field name for ''.", ''];
        yield 'null' => ['Cannot determine the field name for null.', null];
        yield 'array' => ['Cannot determine the field name for array.', ['increment_id']];
        yield 'attribute without code' => [
            'Cannot determine the field name for Mage_Eav_Model_Entity_Attribute.',
            new Mage_Eav_Model_Entity_Attribute(),
        ];
    }
}
