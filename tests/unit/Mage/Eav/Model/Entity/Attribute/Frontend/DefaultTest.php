<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Eav\Model\Entity\Attribute\Frontend;

use Mage;
use Mage_Eav_Model_Entity_Attribute_Frontend_Default as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class DefaultTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('eav/entity_attribute_frontend_default');
    }
}
