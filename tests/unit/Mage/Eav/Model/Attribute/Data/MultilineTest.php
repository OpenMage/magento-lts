<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Attribute\Data;

use Mage;
use Mage_Eav_Model_Attribute_Data_Multiline as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class MultilineTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('eav/attribute_data_multiline');
    }
}
