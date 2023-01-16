<?php
declare(strict_types=1);

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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default rss helper
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Helper_Config extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Adminhtml';

    /**
     * @var array
     */
    protected $defaultBackendClassMap = [
        'color' => 'adminhtml/system_config_backend_color'
    ];

    /**
     * Get field backend model
     *
     * @param Varien_Simplexml_Element $fieldConfig
     * @return string
     */
    public function getBackendClass(Varien_Simplexml_Element $fieldConfig): string
    {
        $backendClass = (isset($fieldConfig->backend_model)) ? $fieldConfig->backend_model : false;
        if ($backendClass) {
            return (string) $backendClass;
        }

        if (isset($fieldConfig->frontend_type, $this->defaultBackendClassMap[(string)$fieldConfig->frontend_type])) {
            return $this->defaultBackendClassMap[(string)$fieldConfig->frontend_type];
        }

        return 'core/config_data';
    }
}
