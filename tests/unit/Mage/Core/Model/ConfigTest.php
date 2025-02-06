<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model;

use Mage;
use Mage_Core_Model_Config as Subject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/config');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testSaveDeleteGetConfig(): void
    {
        $path = 'test/config';
        $value = 'foo';

        $this->assertFalse($this->subject->getConfig($path));

        $this->subject->saveConfig($path, $value);
        $this->assertSame($value, $this->subject->getConfig($path));

        $this->subject->deleteConfig($path);
        $this->assertFalse($this->subject->getConfig($path));
    }
}
