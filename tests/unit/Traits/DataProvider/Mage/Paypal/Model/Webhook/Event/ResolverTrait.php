<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Paypal\Model\Webhook\Event;

trait ResolverTrait
{
    /**
     * @return array<string, array{0: array<string, mixed>, 1: array<string, mixed>, 2: string[]}>
     */
    public function provideCandidateIdsData(): array
    {
        return [
            'event and resource ids are de-duplicated' => [
                [
                    'resource' => [
                        'id' => 'CAPTURE-1',
                        'parent_payment' => 'PAYMENT-1',
                        'invoice_id' => '100000001',
                        'supplementary_data' => [
                            'related_ids' => [
                                'order_id' => 'ORDER-1',
                                'authorization_id' => 'AUTH-1',
                                'capture_id' => 'CAPTURE-1',
                                'refund_id' => '',
                            ],
                        ],
                    ],
                ],
                [
                    'resource_id' => 'RESOURCE-1',
                    'paypal_order_id' => 'ORDER-1',
                    'paypal_capture_id' => 'CAPTURE-1',
                    'paypal_authorization_id' => 'AUTH-1',
                    'paypal_refund_id' => 'REFUND-1',
                ],
                [
                    'RESOURCE-1',
                    'ORDER-1',
                    'CAPTURE-1',
                    'AUTH-1',
                    'REFUND-1',
                    'PAYMENT-1',
                    '100000001',
                ],
            ],
        ];
    }

    /**
     * @return array<string, array{0: array<string, mixed>, 1: string[]}>
     */
    public function provideIncrementIdsData(): array
    {
        return [
            'resource and purchase unit order references' => [
                [
                    'resource' => [
                        'invoice_id' => '100000001',
                        'custom_id' => 'custom-1',
                        'purchase_units' => [
                            [
                                'invoice_id' => '100000002',
                                'reference_id' => '100000003',
                                'custom_id' => 'custom-2',
                            ],
                            [
                                'invoice_id' => '100000001',
                                'reference_id' => '',
                            ],
                        ],
                    ],
                ],
                [
                    '100000001',
                    'custom-1',
                    '100000002',
                    '100000003',
                    'custom-2',
                ],
            ],
        ];
    }
}
