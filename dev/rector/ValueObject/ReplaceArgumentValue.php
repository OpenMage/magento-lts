<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\ValueObject;

use PHPStan\Type\ObjectType;
use Rector\Validation\RectorAssert;

/**
 * Replaces a scalar argument value at a given position of a method call
 */
final class ReplaceArgumentValue
{
    /**
     * @param class-string $class
     * @param int<0, max>  $position
     */
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        public readonly int $position,
        public readonly null|bool|float|int|string $valueBefore,
        public readonly null|bool|ClassConstant|float|int|string $valueAfter,
    ) {
        RectorAssert::className($class);
    }

    public function getObjectType(): ObjectType
    {
        return new ObjectType($this->class);
    }
}
