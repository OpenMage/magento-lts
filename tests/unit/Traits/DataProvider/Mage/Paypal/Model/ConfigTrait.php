<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model;

trait ConfigTrait
{
    /**
     * @return array<string, array{0: int, 1: string}>
     */
    public function provideApiTimeoutData(): array
    {
        return [
            'disabled SDK default' => [0, '0'],
            'positive timeout' => [15, '15'],
            'negative timeout is clamped' => [0, '-3'],
        ];
    }

    /**
     * @return array<string, array{
     *     0: array{
     *         enabled: bool,
     *         number_of_retries: int,
     *         retry_interval: float,
     *         backoff_factor: float,
     *         maximum_retry_wait_time: int,
     *         retry_on_timeout: bool,
     *         http_status_codes: int[],
     *         http_methods: string[]
     *     },
     *     1: array<string, string>
     * }>
     */
    public function provideGetRetryConfigurationData(): array
    {
        return [
            'defaults' => [
                [
                    'enabled' => false,
                    'number_of_retries' => 0,
                    'retry_interval' => 1.0,
                    'backoff_factor' => 2.0,
                    'maximum_retry_wait_time' => 0,
                    'retry_on_timeout' => true,
                    'http_status_codes' => [408, 413, 429, 500, 502, 503, 504, 521, 522, 524],
                    'http_methods' => ['GET', 'PUT'],
                ],
                [
                    'retry_enabled' => '0',
                    'retry_count' => '0',
                    'retry_interval' => '1',
                    'retry_backoff_factor' => '2',
                    'retry_max_wait_time' => '0',
                    'retry_on_timeout' => '1',
                    'retry_status_codes' => '408,413,429,500,502,503,504,521,522,524',
                    'retry_http_methods' => 'GET,PUT',
                ],
            ],
            'custom values are normalized' => [
                [
                    'enabled' => true,
                    'number_of_retries' => 3,
                    'retry_interval' => 0.5,
                    'backoff_factor' => 1.5,
                    'maximum_retry_wait_time' => 10,
                    'retry_on_timeout' => false,
                    'http_status_codes' => [408, 429, 500],
                    'http_methods' => ['GET', 'POST', 'PUT'],
                ],
                [
                    'retry_enabled' => '1',
                    'retry_count' => '3',
                    'retry_interval' => '0.5',
                    'retry_backoff_factor' => '1.5',
                    'retry_max_wait_time' => '10',
                    'retry_on_timeout' => '0',
                    'retry_status_codes' => '408, 429, 500,429,ignored,99,600',
                    'retry_http_methods' => 'get, post, put,trace,get',
                ],
            ],
            'negative numeric values are clamped' => [
                [
                    'enabled' => true,
                    'number_of_retries' => 0,
                    'retry_interval' => 0.0,
                    'backoff_factor' => 0.0,
                    'maximum_retry_wait_time' => 0,
                    'retry_on_timeout' => true,
                    'http_status_codes' => [],
                    'http_methods' => [],
                ],
                [
                    'retry_enabled' => '1',
                    'retry_count' => '-1',
                    'retry_interval' => '-0.1',
                    'retry_backoff_factor' => '-2',
                    'retry_max_wait_time' => '-10',
                    'retry_on_timeout' => '1',
                    'retry_status_codes' => 'invalid',
                    'retry_http_methods' => 'unknown',
                ],
            ],
        ];
    }

    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public function provideSdkHttpDebugData(): array
    {
        return [
            'disabled' => [false, '0'],
            'enabled' => [true, '1'],
        ];
    }
}
