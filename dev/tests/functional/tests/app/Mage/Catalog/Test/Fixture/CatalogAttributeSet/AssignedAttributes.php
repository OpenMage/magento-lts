<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\Fixture\CatalogAttributeSet;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Fixture\FixtureFactory;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Preset for assigned attributes.
 *  Data keys:
 *  - dataset
 *  - attributes
 */
class AssignedAttributes extends DataSource
{
    /**
     * Assigned attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataset']) && is_string($data['dataset'])) {
            $dataset = explode(',', $data['dataset']);
            foreach ($dataset as $preset) {
                /** @var CatalogProductAttribute $attribute */
                $attribute = $fixtureFactory->createByCode('catalogProductAttribute', ['dataset' => $preset]);
                $attribute->persist();

                $this->data[] = $attribute->getAttributeCode();
                $this->attributes[] = $attribute;
            }
        } elseif (isset($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attribute) {
                /** @var CatalogProductAttribute $attribute */
                $this->data[] = $attribute->getAttributeCode();
                $this->attributes[] = $attribute;
            }
        } else {
            $this->data = $data;
        }
    }

    /**
     * Get Attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
