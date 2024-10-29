<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   OpenMage
 * @package    OpenMage_Tests
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Page\Model;

use Mage;
use Mage_Page_Model_Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public Mage_Page_Model_Config $subject;

    public function setUp(): void
    {
        Mage::app();
        $this->subject = Mage::getModel('page/config');
    }

    /**
     * @group Mage_Page
     * @group Mage_Page_Model
     */
    public function testGetPageLayoutHandles(): void
    {
        $this->assertIsArray($this->subject->getPageLayoutHandles());
    }
}
