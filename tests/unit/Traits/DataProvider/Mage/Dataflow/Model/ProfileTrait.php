<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model;

use Generator;

trait ProfileTrait
{
    /**
     * Profile data, number of actions, expected var values, expected map entries.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideParseGuiDataData(): Generator
    {
        $closeCdata = 'x]]></var></action><action type="core/convert_adapter_http_curl" method="load"><var name="uri"><![CDATA[http://evil';
        $closeElement = 'a</var></action><action type="core/convert_adapter_http_curl" method="load"><var name="uri">http://evil';

        yield 'export to a local file' => [
            [
                'entity_type' => 'product',
                'direction' => 'export',
                'data_transfer' => 'file',
                'store_id' => 0,
                'gui_data' => [
                    'file' => ['type' => 'file', 'path' => $closeElement, 'filename' => $closeCdata],
                    'parse' => ['type' => 'csv', 'delimiter' => ',', 'enclose' => '"', 'fieldnames' => $closeElement],
                    'map' => [
                        'product' => ['db' => ['sku'], 'file' => [$closeCdata]],
                        'only_specified' => $closeElement,
                    ],
                    'product' => ['filter' => ['name' => $closeCdata, 'price' => ['from' => $closeCdata]]],
                    'export' => ['add_url_field' => $closeCdata],
                ],
            ],
            // product load, product unparse, column map, csv unparse, io save
            5,
            [
                'path' => $closeElement,
                'filename' => $closeCdata,
                'fieldnames' => $closeElement,
                '_only_specified' => $closeElement,
                'filter/name' => $closeCdata,
                'filter/price/from' => $closeCdata,
                'url_field' => $closeCdata,
            ],
            ['sku' => $closeCdata],
        ];

        yield 'import from ftp' => [
            [
                'entity_type' => 'customer',
                'direction' => 'import',
                'data_transfer' => 'file',
                'store_id' => 1,
                'gui_data' => [
                    'file' => [
                        'type' => 'ftp',
                        'path' => '/in',
                        'filename' => 'customers.csv',
                        'host' => $closeCdata,
                        'user' => $closeCdata,
                        'password' => $closeCdata,
                    ],
                    'parse' => ['type' => 'csv', 'delimiter' => $closeCdata, 'enclose' => $closeCdata, 'fieldnames' => 'true'],
                    'map' => [
                        'customer' => ['db' => [$closeCdata], 'file' => ['email" type="x']],
                        'only_specified' => '',
                    ],
                    'import' => ['number_of_records' => $closeElement, 'decimal_separator' => $closeCdata],
                ],
            ],
            // io load, csv parse
            2,
            [
                'host' => 'x]]></var></action><action type="core/convert_adapter_http_curl" method="load"><var name="uri"><![CDATA[http',
                'user' => $closeCdata,
                'password' => $closeCdata,
                'delimiter' => $closeCdata,
                'enclose' => $closeCdata,
                'number_of_records' => $closeElement,
                'decimal_separator' => $closeCdata,
            ],
            ['email" type="x' => $closeCdata],
        ];
    }

    /**
     * Profile data with a value the profile wizard does not offer.
     *
     * @return Generator<string, array<mixed>>
     */
    public static function provideParseGuiDataInvalidData(): Generator
    {
        $valid = [
            'entity_type' => 'product',
            'direction' => 'export',
            'data_transfer' => 'file',
            'store_id' => 0,
            'gui_data' => [
                'file' => ['type' => 'file', 'path' => 'var/export', 'filename' => 'export.csv'],
                'parse' => ['type' => 'csv', 'delimiter' => ',', 'enclose' => '"', 'fieldnames' => 'true'],
                'map' => ['product' => ['db' => [], 'file' => []], 'only_specified' => ''],
                'product' => ['filter' => []],
            ],
        ];

        $data = $valid;
        $data['entity_type'] = null;
        yield 'advanced profile' => [$data];

        $data = $valid;
        $data['gui_data']['file']['type'] = 'file</var><var name="x">';
        yield 'unknown file type' => [$data];

        $data = $valid;
        $data['gui_data']['parse']['type'] = 'csv2';
        yield 'unknown file format' => [$data];
    }
}
