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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
* Class config
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Maged_Model_Config_Community extends Maged_Model_Config_Abstract implements Maged_Model_Config_Interface
{

    /**
     * Initialization
     */
    protected function _construct()
    {
        $this->load();
    }

    /**
     * Set data for Settings View
     *
     * @param Mage_Connect_Config $config
     * @param Maged_View $view
     * @return null
     */
    public function setInstallView($config, $view)
    {
        $view->set('channel_logo', 'logo');
    }

    /**
     * Set data for Settings View
     * @param Mage_Connect_Config $config
     * @param Maged_View $view
     * @return null
     */
    public function setSettingsView($config, $view)
    {
    }

    /**
     * Set session data for Settings
     * @param array $post post data
     * @param mixed $session Session object
     * @return null
     */
    public function setSettingsSession($post, $session)
    {
    }

    /**
     * Get root channel URI
     *
     * @return string Root channel URI
     */
    public function getRootChannelUri(){
        if (!$this->get('root_channel_uri')) {
            $this->set('root_channel_uri', 'connect20.magentocommerce.com/community');
        }
        return $this->get('root_channel_uri');
    }

    /**
     * Set config data from POST
     *
     * @param Mage_Connect_Config $config Config object
     * @param array $post post data
     * @return null
     */
    public function setPostData($config, &$post)
    {
    }

    /**
     * Set additional command options
     *
     * @param mixed $session Session object
     * @param array $options
     * @return null
     */
    public function setCommandOptions($session, &$options)
    {
    }
}
?>
