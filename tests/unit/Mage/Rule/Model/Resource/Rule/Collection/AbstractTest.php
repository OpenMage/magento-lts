<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Rule\Model\Resource\Rule\Collection;

// use Mage;
// use Mage_Rule_Model_Resource_Rule_Collection_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Rule\Model\Resource\Rule\Collection\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('rule/resource_rule_collection_abstract');
        self::markTestSkipped('');
    }
}
