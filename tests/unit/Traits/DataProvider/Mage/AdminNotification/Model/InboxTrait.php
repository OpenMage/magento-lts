<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
