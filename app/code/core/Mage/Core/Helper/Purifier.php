<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 */
class Mage_Core_Helper_Purifier extends Mage_Core_Helper_Abstract
{
    public const CACHE_DEFINITION = 'Cache.DefinitionImpl';

    protected ?HTMLPurifier $purifier;

    /**
     * Purifier Constructor Call
     */
    public function __construct(
        ?HTMLPurifier $purifier = null
    ) {
        $config = HTMLPurifier_Config::createDefault();
        $config->set(self::CACHE_DEFINITION, null);
        $this->purifier = $purifier ?? new HTMLPurifier($config);
    }

    /**
     * Purify Html Content
     *
     * @param array|string $content
     * @return array|string
     */
    public function purify($content)
    {
        return is_array($content) ? $this->purifier->purifyArray($content) : $this->purifier->purify($content);
    }
}
