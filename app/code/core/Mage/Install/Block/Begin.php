<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installation begin block
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Block_Begin extends Mage_Install_Block_Abstract
{
    /**
     * Set template
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/begin.phtml');
    }

    /**
     * @deprecated
     */
    public function getLanguages() {}

    /**
     * Get wizard URL
     *
     * @return string
     */
    public function getPostUrl()
    {
        return Mage::getUrl('install/wizard/beginPost');
    }

    /**
     * Get License HTML contents
     *
     * @return string
     */
    public function getLicenseHtml()
    {
        return nl2br(file_get_contents(BP . DS . (string) Mage::getConfig()->getNode('install/eula_file')));
    }
}
