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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installation wizard model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Wizard
{
    /**
     * Wizard configuration
     *
     * @var array
     */
    protected $_steps = [];

    public function __construct()
    {
        $this->_steps = Mage::getSingleton('install/config')->getWizardSteps();

        foreach ($this->_steps as $index => $step) {
            $this->_steps[$index]->setUrl(
                $this->_getUrl($this->_steps[$index]->getController(), $this->_steps[$index]->getAction())
            );

            if (isset($this->_steps[$index + 1])) {
                $this->_steps[$index]->setNextUrl(
                    $this->_getUrl($this->_steps[$index + 1]->getController(), $this->_steps[$index + 1]->getAction())
                );
                $this->_steps[$index]->setNextUrlPath(
                    $this->_getUrlPath($this->_steps[$index + 1]->getController(), $this->_steps[$index + 1]->getAction())
                );
            }
            if (isset($this->_steps[$index - 1])) {
                $this->_steps[$index]->setPrevUrl(
                    $this->_getUrl($this->_steps[$index - 1]->getController(), $this->_steps[$index - 1]->getAction())
                );
                $this->_steps[$index]->setPrevUrlPath(
                    $this->_getUrlPath($this->_steps[$index - 1]->getController(), $this->_steps[$index - 1]->getAction())
                );
            }
        }
    }

    /**
     * Get wizard step by request
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @return  Varien_Object | false
     */
    public function getStepByRequest(Zend_Controller_Request_Abstract $request)
    {
        foreach ($this->_steps as $step) {
            if ($step->getController() == $request->getControllerName()
                    && $step->getAction() == $request->getActionName()) {
                return $step;
            }
        }
        return false;
    }

    /**
     * Get wizard step by name
     *
     * @param   string $name
     * @return  Varien_Object | false
     */
    public function getStepByName($name)
    {
        foreach ($this->_steps as $step) {
            if ($step->getName() == $name) {
                return $step;
            }
        }
        return false;
    }

    /**
     * Get all wizard steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }

    protected function _getUrl($controller, $action)
    {
        return Mage::getUrl($this->_getUrlPath($controller, $action));
    }

    /**
     * Retrieve Url Path
     *
     * @param string $controller
     * @param string $action
     * @return string
     */
    protected function _getUrlPath($controller, $action)
    {
        return 'install/' . $controller . '/' . $action;
    }
}
