<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Sales\Block\Adminhtml\Customer\Edit\Tab\Recurring;

use Mage_Sales_Block_Adminhtml_Customer_Edit_Tab_Recurring_Profile as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Sales\Block\Adminhtml\Customer\Edit\Tab\Recurring\ProfileTrait;

final class ProfileTest extends OpenMageTest
{
    use ProfileTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
