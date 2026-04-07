<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Api\Model\Resource;

// use Mage;
// use Mage_Api_Model_Resource_Abstract as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Api\Model\Resource\AbstractTrait;

final class AbstractTest extends OpenMageTest
{
    use AbstractTrait;

    // private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('api/resource_abstract');
        self::markTestSkipped('');
    }
}
