<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Newsletter;

use Generator;

/**
 * @phpstan-type ValidateData array{
 *     "template_code": ?string,
 *     "template_sender_email": ?string,
 *     "template_sender_name": ?string,
 *     "template_subject": ?string,
 *     "template_text": ?string,
 *     "template_type": ?int
 * }
 */
trait TemplateTrait
{

    /**
     * @return Generator<string, list{null|string, ValidateData}, void, void>
     */
    public static function provideValidateData(): Generator
    {
        $validData = [
            'template_code'           => 'Valid Code',
            'template_sender_email'   => 'test@example.com',
            'template_sender_name'    => 'Sender Name',
            'template_subject'        => 'Valid Subject',
            'template_text'           => 'Valid Template Text',
            'template_type'           => 1,
        ];

        yield 'valid data' => [
            null,
            $validData,
        ];

        $data = $validData;
        $data['template_code'] = null;
        yield 'missing template code' => [
            "You must give a non-empty value for field 'template_code'",
            $data,
        ];

        $data = $validData;
        $data['template_sender_email'] = null;
        yield 'missing sender email' => [
            "You must give a non-empty value for field 'template_sender_email'",
            $data,
        ];

        $data = $validData;
        $data['template_sender_email'] = 'invalid-email';
        yield 'invalid sender email' => [
            "You must give a non-empty value for field 'template_sender_email'",
            $data,
        ];

        $data = $validData;
        $data['template_sender_name'] = null;
        yield 'missing sender name' => [
            "You must give a non-empty value for field 'template_sender_name'",
            $data,
        ];

        $data = $validData;
        $data['template_subject'] = null;
        yield 'missing template subject' => [
            null,
            $data,
        ];

        $data = $validData;
        $data['template_text'] = null;
        yield 'missing template text' => [
            null,
            $data,
        ];

        $data = $validData;
        $data['template_type'] = 999;
        yield 'invalid template type' => [
            'The value 999 you selected for "template_type" is not a valid choices 1, 2.',
            $data,
        ];
    }
}
