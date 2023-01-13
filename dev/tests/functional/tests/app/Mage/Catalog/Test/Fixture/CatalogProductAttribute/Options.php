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

namespace Mage\Catalog\Test\Fixture\CatalogProductAttribute;

use Magento\Mtf\Fixture\DataSource;
use Magento\Mtf\Repository\RepositoryFactory;

/**
 * Prepare Manage Options for attribute.
 */
class Options extends DataSource
{
    /**
     * Options ids.
     *
     * @var array
     */
    protected $optionsIds = [];

    /**
     * @constructor
     * @param RepositoryFactory $repositoryFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(RepositoryFactory $repositoryFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataset']) && isset($this->params['repository'])) {
            $this->data = $repositoryFactory->get($this->params['repository'])->get($data['dataset']);
        } elseif (isset($data['value'])) {
            $this->data = $data['value'];
        }

        $this->optionsIds = isset($data['optionsIds']) ? $data['optionsIds'] : $this->optionsIds;
    }

    /**
     * Get options ids.
     *
     * @return array
     */
    public function getOptionsIds()
    {
        return $this->optionsIds;
    }
}
