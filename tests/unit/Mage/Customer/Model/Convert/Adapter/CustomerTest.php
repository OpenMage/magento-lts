<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model\Convert\Adapter;

use Override;
use Mage;
use Mage_Customer_Model_Convert_Adapter_Customer as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use Throwable;

final class CustomerTest extends OpenMageTest
{
    private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('customer/convert_adapter_customer');
    }

    /**
     * @throws Throwable
     * @group Model
     */
    public function testSaveRow(): void
    {
        $data = [
            'website'   => 'base',
            'email'     => 'test@example.com',
            'group'     => 'General',
            'firstname' => 'John',
            'lastname'  => 'Doe',
        ];
        self::assertInstanceOf(Subject::class, self::$subject->saveRow($data));
    }
}
