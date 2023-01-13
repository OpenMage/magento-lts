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
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Install config
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Config extends Varien_Simplexml_Config
{
    public const XML_PATH_WIZARD_STEPS     = 'wizard/steps';
    public const XML_PATH_CHECK_WRITEABLE  = 'check/filesystem/writeable';
    public const XML_PATH_CHECK_EXTENSIONS = 'check/php/extensions';

    protected $_optionsMapping = [self::XML_PATH_CHECK_WRITEABLE => [
        'app_etc' => 'etc_dir',
        'var'     => 'var_dir',
        'media'   => 'media_dir',
    ]];

    public function __construct()
    {
        parent::__construct();
        $this->loadString('<?xml version="1.0"?><config></config>');
        Mage::getConfig()->loadModulesConfiguration('install.xml', $this);
    }

    /**
     * Get array of wizard steps
     *
     * array($inndex => Varien_Object )
     *
     * @return array
     */
    public function getWizardSteps()
    {
        $steps = [];
        foreach ((array)$this->getNode(self::XML_PATH_WIZARD_STEPS) as $stepName => $step) {
            $stepObject = new Varien_Object((array)$step);
            $stepObject->setName($stepName);
            $steps[] = $stepObject;
        }
        return $steps;
    }

    /**
     * Retrieve writable path for checking
     *
     * array(
     *      ['writeable'] => array(
     *          [$index] => array(
     *              ['path']
     *              ['recursive']
     *          )
     *      )
     * )
     *
     * @deprecated since 1.7.1.0
     *
     * @return array
     */
    public function getPathForCheck()
    {
        $res = [];

        $items = (array) $this->getNode(self::XML_PATH_CHECK_WRITEABLE);

        foreach ($items as $item) {
            $res['writeable'][] = (array) $item;
        }

        return $res;
    }

    /**
     * Retrieve writable full paths for checking
     *
     * @return array
     */
    public function getWritableFullPathsForCheck()
    {
        $paths = [];
        $items = (array) $this->getNode(self::XML_PATH_CHECK_WRITEABLE);
        foreach ($items as $nodeKey => $item) {
            $value = (array)$item;
            if (isset($this->_optionsMapping[self::XML_PATH_CHECK_WRITEABLE][$nodeKey])) {
                $configKey = $this->_optionsMapping[self::XML_PATH_CHECK_WRITEABLE][$nodeKey];
                $value['path'] = Mage::app()->getConfig()->getOptions()->getData($configKey);
            } else {
                $value['path'] = dirname(Mage::getRoot()) . $value['path'];
            }
            $paths[$nodeKey] = $value;
        }

        return $paths;
    }

    /**
     * Retrieve required PHP extensions
     *
     * @return array
     */
    public function getExtensionsForCheck()
    {
        $res = [];
        $items = (array) $this->getNode(self::XML_PATH_CHECK_EXTENSIONS);

        foreach ($items as $name => $value) {
            if (!empty($value)) {
                $res[$name] = [];
                foreach ($value as $subname => $subvalue) {
                    $res[$name][] = $subname;
                }
            } else {
                $res[$name] = (array) $value;
            }
        }

        return $res;
    }
}
