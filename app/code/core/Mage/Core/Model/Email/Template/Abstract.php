<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Core_Model_Email_Template_Abstract extends Mage_Core_Model_Template
{
    const XML_PATH_DESIGN_EMAIL_LOGO            = 'design/email/logo';
    const XML_PATH_DESIGN_EMAIL_LOGO_ALT        = 'design/email/logo_alt';
    const XML_PATH_DESIGN_EMAIL_LOGO_WIDTH      = 'design/email/logo_width';
    const XML_PATH_DESIGN_EMAIL_LOGO_HEIGHT     = 'design/email/logo_height';
    const XML_PATH_CSS_NON_INLINE_FILES         = 'design/email/css_non_inline';

    protected $_cssFileCache = array();

    /**
     * Get template code for template directive
     *
     * @param   string $configPath
     * @param   array $variables
     * @return  string
     */
    public function getTemplateByConfigPath($configPath, array $variables)
    {
        $template = Mage::getModel('core/email_template');
        $template->loadByConfigPath($configPath, $variables);

        return $template->getProcessedTemplate($variables);
    }

    /**
     * Load template by configuration path. This enables html templates to include other html templates by their
     * system configuration XPATH value
     *
     * @param   string $configPath The path to the config setting that defines which global/template/email/* node
     * should be used to load the email template
     * @return   Mage_Core_Model_Email_Template
     */
    public function loadByConfigPath($configPath)
    {
        $templateId = Mage::getStoreConfig($configPath);

        if (is_numeric($templateId)) {
            // Template was overridden in admin, so load template from database
            $this->load($templateId);
        } else {
            $defaultTemplates = Mage_Core_Model_Email_Template::getDefaultTemplates();
            if (!isset($defaultTemplates[$templateId])) {
                return null;
            }

            $storeId = $this->getDesignConfig()->getStore();

            $data = &$defaultTemplates[$templateId];
            $this->setTemplateType($data['type']=='html' ? self::TYPE_HTML : self::TYPE_TEXT);

            $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
            $templateText = Mage::app()->getTranslator()->getTemplateFile(
                $data['file'], 'email', $localeCode
            );

            $this->setTemplateText($templateText);
            $this->setId($templateId);
        }

        // Templates loaded via the {{template config_path=""}} syntax don't support the subject/vars/styles
        // comment blocks, so strip them out
        $templateText = preg_replace('/<!--@(\w+)\s*(.*?)\s*@-->/us', '', $this->getTemplateText());
        // Remove comment lines
        $templateText = preg_replace('#\{\*.*\*\}#suU', '', $templateText);

        $this->setTemplateText($templateText);

        return $this;
    }

    /**
     * Return logo URL for emails
     * Take logo from skin if custom logo is undefined
     *
     * @param  Mage_Core_Model_Store|int|string $store
     * @return string
     */
    protected function _getLogoUrl($store)
    {
        $store = Mage::app()->getStore($store);
        $fileName = $store->getConfig(self::XML_PATH_DESIGN_EMAIL_LOGO);
        if ($fileName) {
            $uploadDir = Mage_Adminhtml_Model_System_Config_Backend_Email_Logo::UPLOAD_DIR;
            $fullFileName = Mage::getBaseDir('media') . DS . $uploadDir . DS . $fileName;
            if (file_exists($fullFileName)) {
                return Mage::getBaseUrl('media') . $uploadDir . '/' . $fileName;
            }
        }
        return Mage::getDesign()->getSkinUrl('images/logo_email.gif');
    }

    /**
     * Return logo alt for emails
     *
     * @param  Mage_Core_Model_Store|int|string $store
     * @return string
     */
    protected function _getLogoAlt($store)
    {
        $store = Mage::app()->getStore($store);
        $alt = $store->getConfig(self::XML_PATH_DESIGN_EMAIL_LOGO_ALT);
        if ($alt) {
            return $alt;
        }
        return $store->getFrontendName();
    }

    /**
     * Add variables that are used by transactional emails and newsletter emails
     *
     * @param $variables
     * @param $storeId
     * @return mixed
     */
    protected function _addEmailVariables($variables, $storeId)
    {
        if (!isset($variables['store'])) {
            $variables['store'] = Mage::app()->getStore($storeId);
        }
        if (!isset($variables['logo_url'])) {
            $variables['logo_url'] = $this->_getLogoUrl($storeId);
        }
        if (!isset($variables['logo_alt'])) {
            $variables['logo_alt'] = $this->_getLogoAlt($storeId);
        }

        $defaultValuesMap = array(
            "logo_width" => self::XML_PATH_DESIGN_EMAIL_LOGO_WIDTH,
            "logo_height" => self::XML_PATH_DESIGN_EMAIL_LOGO_HEIGHT,
            "phone" => Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE,
            "store_phone" => Mage_Core_Model_Store::XML_PATH_STORE_STORE_PHONE,
            "store_hours" => Mage_Core_Model_Store::XML_PATH_STORE_STORE_HOURS,
            "store_email" => Mage_Customer_Helper_Data::XML_PATH_SUPPORT_EMAIL,
        );

        foreach ($defaultValuesMap as $variableName => $configValue) {
            if (!isset($variables[$variableName])) {
                $variables[$variableName] = Mage::getStoreConfig($configValue, $storeId);
            }
        }
        // If template is text mode, don't include styles
        if (!$this->isPlain()) {
            $variables['non_inline_styles'] = $this->_getNonInlineCssTag();
        }
        return $variables;
    }

    /**
     * Merge HTML and CSS and returns HTML that has CSS styles applied "inline" to the HTML tags. This is necessary
     * in order to support all email clients.
     *
     * @param $html
     * @return string
     */
    protected function _applyInlineCss($html)
    {
        try {
            // Check to see if the {{inlinecss file=""}} directive set a CSS file to inline
            $inlineCssFile = $this->getInlineCssFile();
            // Only run Emogrify if HTML exists
            if (strlen($html) && $inlineCssFile) {
                $cssToInline = $this->_getCssFileContent($inlineCssFile);
                $emogrifier = new Pelago_Emogrifier();
                $emogrifier->setHtml($html);
                $emogrifier->setCss($cssToInline);
                // Don't parse inline <style> tags, since existing tag is intentionally for no-inline styles
                $emogrifier->setParseInlineStyleTags(false);

                $processedHtml = $emogrifier->emogrify();
            } else {
                $processedHtml = $html;
            }
        } catch (Exception $e) {
            $processedHtml = '{CSS inlining error: ' . $e->getMessage() . '}' . PHP_EOL . $html;
        }
        return $processedHtml;
    }

    /**
     * Load CSS content from filesystem
     *
     * @param string $filename
     * @return string
     */
    protected function _getCssFileContent($filename)
    {
        $storeId = $this->getDesignConfig()->getStore();
        $area = $this->getDesignConfig()->getArea();
        // This method should always be called within the context of the email's store, so these values will be correct
        $package = Mage::getDesign()->getPackageName();
        $theme = Mage::getDesign()->getTheme('skin');

        $filePath = Mage::getDesign()->getFilename(
            'css' . DS . $filename,
            array(
                '_type' => 'skin',
                '_default' => false,
                '_store' => $storeId,
                '_area' => $area,
                '_package' => $package,
                '_theme' => $theme,
            )
        );

        if (is_readable($filePath)) {
            return (string) file_get_contents($filePath);
        }

        // If file can't be found, return empty string
        return '';
    }

    /**
     * Accepts a path to a System Config setting that contains a comma-delimited list of files to load. Loads those
     * files and then returns the concatenated content.
     *
     * @param $configPath
     * @return string
     */
    protected function _getCssByConfig($configPath)
    {
        if (!isset($this->_cssFileCache[$configPath])) {
            $filesToLoad = Mage::getStoreConfig($configPath);
            if (!$filesToLoad) {
                return '';
            }
            $files = array_map('trim', explode(",", $filesToLoad));

            $css = '';
            foreach($files as $fileName) {
                $css .= $this->_getCssFileContent($fileName) . "\n";
            }
            $this->_cssFileCache[$configPath] = $css;
        }

        return $this->_cssFileCache[$configPath];
    }

    /**
     * Loads content of files with non-inline CSS styles and merges them with any CSS styles that are specified
     * within the <!--@styles @--> comments or in the Transactional Emails
     *
     * @return string
     */
    protected function _getNonInlineCssTag()
    {
        $styleTagWrapper = "<style type=\"text/css\">\n%s\n</style>\n";
        // Load the no-inline CSS styles from theme so they can be included in the style tag
        $styleTagContent = $this->_getCssByConfig(self::XML_PATH_CSS_NON_INLINE_FILES);
        // Load the CSS that is included in the <!--@styles @--> comment or is added via Transactional Emails in admin
        $styleTagContent .= $this->getTemplateStyles();
        return sprintf($styleTagWrapper, $styleTagContent);
    }
}
