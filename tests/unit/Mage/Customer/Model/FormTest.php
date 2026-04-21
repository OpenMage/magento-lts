<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Customer\Model;

// use Mage;
// use Mage_Customer_Model_Form as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Customer\Model\FormTrait;

final class FormTest extends OpenMageTest
{
    use FormTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('customer/form');
        self::markTestSkipped('');
    }
}
