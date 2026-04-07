<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Model\Customer\Renderer;

// use Mage;
// use Mage_Adminhtml_Model_Customer_Renderer_Region as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\Customer\Renderer\RegionTrait;

final class RegionTest extends OpenMageTest
{
    use RegionTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('adminhtml/customer_renderer_region');
        self::markTestSkipped('');
    }
}
