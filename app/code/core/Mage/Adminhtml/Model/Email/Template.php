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
 * Adminhtml email template model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * Xml path to email template nodes
     *
     */
    public const XML_PATH_TEMPLATE_EMAIL = '//sections/*/groups/*/fields/*[source_model="adminhtml/system_config_source_email_template"]';

    /**
     * Collect all system config pathes where current template is used as default
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedAsDefault()
    {
        $templateCode = $this->getOrigTemplateCode();
        if (!$templateCode) {
            return [];
        }

        $paths = [];
        Mage::getSingleton('adminhtml/config')->getSections();

        // find nodes which are using $templateCode value
        $defaultCfgNodes = Mage::getConfig()->getXpath('default/*/*[*="' . $templateCode . '"]');
        if (!is_array($defaultCfgNodes)) {
            return [];
        }

        foreach ($defaultCfgNodes as $node) {
            // create email template path in system.xml
            $sectionName = $node->getParent()->getName();
            $groupName = $node->getName();
            $fieldName = substr($templateCode, strlen($sectionName . '_' . $groupName . '_'));
            $paths[] = ['path' => implode('/', [$sectionName, $groupName, $fieldName])];
        }
        return $paths;
    }

    /**
     * Collect all system config pathes where current template is currently used
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedCurrently()
    {
        $templateId = $this->getId();
        if (!$templateId) {
            return [];
        }

        $paths = [];
        $configSections = Mage::getSingleton('adminhtml/config')->getSections();

        // look for node entries in all system.xml that use source_model=adminhtml/system_config_source_email_template
        // they are will be templates, what we try find
        $sysCfgNodes = $configSections->xpath(self::XML_PATH_TEMPLATE_EMAIL);
        if (!is_array($sysCfgNodes)) {
            return [];
        }

        foreach ($sysCfgNodes as $fieldNode) {
            $groupNode = $fieldNode->getParent()->getParent();
            $sectionNode = $groupNode->getParent()->getParent();

            // create email template path in system.xml
            $sectionName = $sectionNode->getName();
            $groupName = $groupNode->getName();
            $fieldName = $fieldNode->getName();

            $paths[] = implode('/', [$sectionName, $groupName, $fieldName]);
        }

        $configData = $this->_getResource()->getSystemConfigByPathsAndTemplateId($paths, $templateId);
        if (!$configData) {
            return [];
        }

        return $configData;
    }

    /**
     * Delete current usage
     *
     * @return $this
     */
    protected function _afterDelete()
    {
        $paths = $this->getSystemConfigPathsWhereUsedCurrently();
        foreach ($paths as $path) {
            $configDataCollection = Mage::getModel('core/config_data')
                ->getCollection()
                ->addFieldToFilter('scope', $path['scope'])
                ->addFieldToFilter('scope_id', $path['scope_id'])
                ->addFieldToFilter('path', $path['path']);
            foreach ($configDataCollection as $configItem) {
                $configItem->delete();
            }
        }
        return parent::_afterDelete();
    }
}
