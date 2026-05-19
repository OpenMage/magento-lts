<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\System\Config\Backend;

trait RetryStatusCodesTrait
{
    /**
     * @return array<string, array{0: string, 1: mixed}>
     */
    public function provideValidRetryStatusCodesData(): array
    {
        return [
            'comma-separated string' => ['408,429,500', '408, 429,500'],
            'array value' => ['502,503', ['502', '503']],
            'duplicates removed' => ['521,522', '521,522,521'],
            'empty value' => ['', ''],
        ];
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public function provideInvalidRetryStatusCodesData(): array
    {
        return [
            'non-integer' => ['408,foo'],
            'decimal' => ['408.5'],
            'below HTTP range' => ['99'],
            'above HTTP range' => ['600'],
        ];
    }
}
