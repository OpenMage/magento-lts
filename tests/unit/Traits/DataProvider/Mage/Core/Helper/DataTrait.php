<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Helper;

use Generator;

trait DataTrait
{
    public function provideFormatTimezoneDate(): Generator
    {
        /** @phpstan-ignore method.nonObject */
        $date           = date_create()->getTimestamp();
        $dateShort      = date('n/j/Y', $date);
        $dateLong       = date('F j, Y', $date);
        $dateShortTime  = date('n/j/Y g:i A', $date);

        yield 'null' => [
            $dateShort,
            null,
        ];
        yield 'empty date' => [
            $dateShort,
            '',
        ];
        yield 'string date' => [
            $dateShort,
            'now',
        ];
        yield 'numeric date' => [
            $dateShort,
            '0',
        ];
        yield 'invalid date' => [
            '',
            'invalid',
        ];
        yield 'invalid format' => [
            (string) $date,
            $date,
            'invalid',
        ];
        yield 'date short' => [
            $dateShort,
            $date,
        ];
        yield 'date long' => [
            $dateLong,
            $date,
            'long',
        ];
        //        yield 'date short w/ time' => [
        //            $dateShortTime,
        //            $date,
        //            'short',
        //            true,
        //        ];
    }

    public function provideRemoveAccents(): Generator
    {
        $string = 'Ae-Ä Oe-Ö Ue-Ü ae-ä oe-ö ue-ü';

        yield 'german false' => [
            'Ae-A Oe-O Ue-U ae-a oe-o ue-u',
            $string,
            false,
        ];
        yield 'german true' => [
            'Ae-Ae Oe-Oe Ue-Ue ae-ae oe-oe ue-ue',
            $string,
            true,
        ];
    }

    public function provideIsCountryInEUData(): Generator
    {
        yield 'DE true' => [
            true,
            'DE',
        ];
        yield 'US false' => [
            false,
            'US',
        ];
    }
}
