<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Block\Customer\Edit\Tab;

use Mage_Adminhtml_Block_Customer_Edit_Tab_Newsletter as Subject;
use Mage_Customer_Model_Customer;
use OpenMage\Tests\Unit\OpenMageTest;

final class NewsletterTest extends OpenMageTest
{
    /**
     * @group Block
     */
    public function testInitForm(): void
    {
        $methods = [
            'getRegistryCurrentCustomer' => new Mage_Customer_Model_Customer(),
        ];
        $mock = $this->getMockWithCalledMethods(Subject::class, $methods);

        self::assertInstanceOf(Subject::class, $mock);
        self::assertInstanceOf(Subject::class, $mock->initForm());
    }
}
