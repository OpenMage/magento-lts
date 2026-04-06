<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\CatalogRule\Model\Resource\Rule;

use Mage;
use Mage_CatalogRule_Model_Resource_Rule_Collection as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\CatalogRule\Model\Resource\Rule\CollectionTrait;

final class CollectionTest extends OpenMageTest
{
    use CollectionTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('catalogrule/resource_rule_collection');
    }
}
