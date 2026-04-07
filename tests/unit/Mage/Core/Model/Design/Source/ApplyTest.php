<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Design\Source;

// use Mage;
// use Mage_Core_Model_Design_Source_Apply as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Core\Model\Design\Source\ApplyTrait;

final class ApplyTest extends OpenMageTest
{
    use ApplyTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('core/design_source_apply');
        self::markTestSkipped('');
    }
}
