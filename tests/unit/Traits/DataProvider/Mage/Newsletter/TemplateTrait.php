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

trait TemplateTrait
{
    public function provideValidateData(): Generator
    {
        $validData = [
            'setTemplateCode'           => 'Valid Code',
            'setTemplateSenderEmail'    => 'test@example.com',
            'setTemplateSenderName'     => 'Sender Name',
            'setTemplateSubject'        => 'Valid Subject',
            'setTemplateText'           => 'Valid Template Text',
            'setTemplateType'           => 1,
        ];

        yield 'valid data' => [
            null,
            $validData,
        ];

        $data = $validData;
        $data['setTemplateCode'] = null;
        yield 'missing template code' => [
            "You must give a non-empty value for field 'template_code'",
            $data,
        ];

        $data = $validData;
        $data['setTemplateSenderEmail'] = null;
        yield 'missing sender email' => [
            "You must give a non-empty value for field 'template_sender_email'",
            $data,
        ];

        $data = $validData;
        $data['setTemplateSenderEmail'] = 'invalid-email';
        yield 'invalid sender email' => [
            "You must give a non-empty value for field 'template_sender_email'",
            $data,
        ];

        $data = $validData;
        $data['setTemplateSenderName'] = null;
        yield 'missing sender name' => [
            "You must give a non-empty value for field 'template_sender_name'",
            $data,
        ];

        $data = $validData;
        $data['setTemplateSubject'] = null;
        yield 'missing template subject' => [
            null,
            $data,
        ];

        $data = $validData;
        $data['setTemplateText'] = null;
        yield 'missing template text' => [
            null,
            $data,
        ];

        $data = $validData;
        $data['setTemplateType'] = 999;
        yield 'invalid template type' => [
            'The value 999 you selected for "template_type" is not a valid choices 1, 2.',
            $data,
        ];
    }
}
