<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api\Model;

use Mage_Api_Model_User as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Model\UserTrait;
use Zend_Validate_Exception;

class UserTest extends OpenMageTest
{
    use UserTrait;

    /**
     * @dataProvider provideValidateApiUserData
     * @param array|true $expectedResult
     * @group Model
     * @throws Zend_Validate_Exception
     */
    public function testValidate($expectedResult, array $methods): void
    {
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        static::assertInstanceOf(Subject::class, $mock);
        static::assertSame($expectedResult, $mock->validate());
    }
}
