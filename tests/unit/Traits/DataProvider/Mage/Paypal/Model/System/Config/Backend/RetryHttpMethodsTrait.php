<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\System\Config\Backend;

trait RetryHttpMethodsTrait
{
    /**
     * @return array<string, array{0: string, 1: mixed}>
     */
    public function provideValidRetryHttpMethodsData(): array
    {
        return [
            'comma-separated string' => ['GET,PUT', 'get, PUT'],
            'multiselect array' => ['GET,POST,PUT', ['get', 'post', 'PUT']],
            'duplicates removed' => ['PATCH,DELETE', 'patch,delete,PATCH'],
            'empty value' => ['', ''],
        ];
    }

    /**
     * @return array<string, array{0: mixed}>
     */
    public function provideInvalidRetryHttpMethodsData(): array
    {
        return [
            'unknown method' => ['TRACE'],
            'mixed valid and invalid methods' => ['GET,OPTIONS'],
        ];
    }
}
