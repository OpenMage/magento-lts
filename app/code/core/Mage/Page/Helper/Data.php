<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Page
 */
class Mage_Page_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_STORE_NOTICE_ENABLED = 'design/head/demonotice';

    public const XML_PATH_STORE_NOTICE_TEXT = 'design/head/store_notice_text';

    public const XML_PATH_BROWSER_CAPABILITIES_JAVASCRIPT = 'web/browser_capabilities/javascript';

    protected $_moduleName = 'Mage_Page';
}
