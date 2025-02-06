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
use Mage_Core_Model_Url as Subject;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public Subject $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('core/url');
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testEscape(): void
    {
        $this->assertSame('%22%27%3E%3C', $this->subject->escape('"\'><'));
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Model
     */
    public function testGetSecure(): void
    {
        $this->assertIsBool($this->subject->getSecure());
    }
}
