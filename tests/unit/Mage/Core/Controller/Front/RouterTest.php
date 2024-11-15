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

namespace OpenMage\Tests\Unit\Mage\Core\Controller\Front;

use Mage;
use Mage_Core_Controller_Front_Router;
use Mage_Core_Model_Config_Element;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public Mage_Core_Controller_Front_Router $subject;

    public function setUp(): void
    {
        Mage::app();
        $config = new Mage_Core_Model_Config_Element('<?xml version="1.0" encoding="utf-8" ?><test-url />');
        $this->subject = new Mage_Core_Controller_Front_Router($config);
    }

    /**
     * @group Mage_Core
     * @group Mage_Core_Controller
     */
    public function testGetUrl(): void
    {
        $this->assertStringEndsWith('test-url/', $this->subject->getUrl());
    }
}
