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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('page.phtml');
        $action = Mage::app()->getFrontController()->getAction();
        if ($action) {
            $this->addBodyClass($action->getFullActionName('-'));
        }
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2));
        }
        return $this->getData('lang');
    }

    /**
     * Add CSS class to page body tag
     *
     * @param string $className
     * @return $this
     */
    public function addBodyClass($className)
    {
        $className = preg_replace('#[^a-z0-9]+#', '-', strtolower($className));
        $this->setBodyClass($this->getBodyClass() . ' ' . $className);
        return $this;
    }
}
