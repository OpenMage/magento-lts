<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api2\Model\Acl\Global\Rule;

use Mage;
use Mage_Api2_Model_Acl_Global_Rule_Tree as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class TreeTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('api2/acl_global_rule_tree');
    }
}
