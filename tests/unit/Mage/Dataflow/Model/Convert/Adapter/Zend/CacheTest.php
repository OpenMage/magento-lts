<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Dataflow\Model\Convert\Adapter\Zend;

use Mage_Dataflow_Model_Convert_Adapter_Zend_Cache as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Dataflow\Model\Convert\Adapter\Zend\CacheTrait;

final class CacheTest extends OpenMageTest
{
    use CacheTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::markTestSkipped('');
    }
}
