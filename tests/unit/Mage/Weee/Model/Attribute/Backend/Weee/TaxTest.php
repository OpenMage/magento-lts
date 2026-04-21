<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Weee\Model\Attribute\Backend\Weee;

// use Mage;
// use Mage_Weee_Model_Attribute_Backend_Weee_Tax as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Weee\Model\Attribute\Backend\Weee\TaxTrait;

final class TaxTest extends OpenMageTest
{
    use TaxTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('weee/attribute_backend_weee_tax');
        self::markTestSkipped('');
    }
}
