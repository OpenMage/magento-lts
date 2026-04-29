<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

declare(strict_types=1);

/**
 * @package Mage_Core
 */
class Mage_Core_Helper_Purifier_DefinitionCache
{
    /** @var null|string */
    protected const DEFINITION_CACHE_MODEL_CLASS = 'core/purifier_definitionCache';

    /** @var null|class-string */
    protected ?string $cacheDefinitionImpl;

    public function __construct()
    {
        if (static::DEFINITION_CACHE_MODEL_CLASS === null) {
            $cacheDefinitionImpl = null;
        } else {
            $cacheModelClassName = Mage::getConfig()->getModelClassName(
                static::DEFINITION_CACHE_MODEL_CLASS,
            );
            if (!class_exists($cacheModelClassName)) {
                throw new RuntimeException(
                    'Invalid Mage Model class alias: '
                    . '"' . static::DEFINITION_CACHE_MODEL_CLASS . '"',
                );
            }

            // Using a short key is not necessary and increases collision risk
            $cacheDefinitionImpl = $cacheModelClassName;

            HTMLPurifier_DefinitionCacheFactory::instance()->register(
                $cacheDefinitionImpl,
                $cacheModelClassName,
            );
        }

        $this->cacheDefinitionImpl = $cacheDefinitionImpl;
    }

    /**
     * @return null|class-string
     */
    public function getCacheDefinitionImpl(): ?string
    {
        return $this->cacheDefinitionImpl;
    }
}
