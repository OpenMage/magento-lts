<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\AdminNotification\Model;

use Generator;
use Mage_AdminNotification_Model_Inbox as Subject;

trait InboxTrait
{
    public function provideGetSeverities(): Generator
    {
        yield 'null' => [
            [
                Subject::SEVERITY_CRITICAL  => 'critical',
                Subject::SEVERITY_MAJOR     => 'major',
                Subject::SEVERITY_MINOR     => 'minor',
                Subject::SEVERITY_NOTICE    => 'notice',
            ],
            null,
        ];
        yield 'valid' => [
            'critical',
            Subject::SEVERITY_CRITICAL,
        ];
        yield 'invalid' => [
            null,
            0,
        ];
    }
}
