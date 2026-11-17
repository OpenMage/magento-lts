<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\ValueObject;

use Rector\Validation\RectorAssert;

/**
 * Type-safe reference to a class constant, used as replacement value
 *
 * Existence of the constant is verified by ReplaceArgumentValueRector via static reflection
 */
final class ClassConstant
{
    /**
     * @param class-string $class
     */
    public function __construct(
        public readonly string $class,
        public readonly string $constant,
    ) {
        RectorAssert::className($class);
    }
}
