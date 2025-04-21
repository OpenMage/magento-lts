<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rule\Model;

use Mage;
use Mage_Rule_Model_Environment as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

class EnvironmentTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('rule/environment');
    }

    /**
     * @covers Mage_Rule_Model_Environment::collect()
     * @group Model
     */
    public function testGetConditionsInstance(): void
    {
        static::assertInstanceOf(Subject::class, self::$subject->collect());
    }
}
