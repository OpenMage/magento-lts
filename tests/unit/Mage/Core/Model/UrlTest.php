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
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Url as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class UrlTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('core/url');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testEscape(): void
    {
        static::assertSame('%22%27%3E%3C', self::$subject->escape('"\'><'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testGetSecure(): void
    {
        static::assertIsBool(self::$subject->getSecure());
    }
}
