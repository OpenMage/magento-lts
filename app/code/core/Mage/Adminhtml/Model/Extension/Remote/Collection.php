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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Adminhtml_Model_Extension_Remote_Collection extends Mage_Adminhtml_Model_Extension_Collection_Abstract
{
    protected function _fetchPackages()
    {
        // fetch installed packages
        $pear = Varien_Pear::getInstance();

        $channels = Mage::getModel('adminhtml/extension')->getKnownChannels();
        $channelData = array();
        foreach ($channels as $channel=>$name) {
            $data = array();
            if (Mage::app()->useCache('pear')) {
                $channelKey = 'PEAR_channel_packages_'.preg_replace('#[^a-z0-9]+#', '_', $channel);
                $data = unserialize(Mage::app()->loadCache($channelKey));
            }
            if (empty($data)) {
                $pear->getFrontend()->clear();
                $pear->run('list-all', array('channel'=>$channel));
                $output = $pear->getOutput();
                if (empty($output)) {
                    continue;
                }
                $data = $output[0]['output'];
                if (Mage::app()->useCache('pear')) {
                    Mage::app()->saveCache(serialize($data), $channelKey, array('pear'), 3600);
                }
            }
            $channelData[$channel] = $data;
        }

        // load available packages into array
        $packages = array();
        foreach ($channelData as $channel) {
            if (!isset($channel['headline'])) {
                continue;
            }
            if (!empty($channel['data'])) {
                foreach ($channel['data'] as $category=>$pkglist) {
                    foreach ($pkglist as $pkg) {
                        $pkgNameArr = explode('/', $pkg[0]);
                        $pkgName = isset($pkgNameArr[1]) ? $pkgNameArr[1] : $pkgNameArr[0];
                        $packages[] = array(
                            'id'=>$channel['channel'].'|'.$pkgName,
                            'category'=>$category,
                            'channel'=>$channel['channel'],
                            'name'=>$pkgName,
                            'remote_version'=>isset($pkg[1]) ? $pkg[1] : '',
                            'local_version'=>isset($pkg[2]) ? $pkg[2] : '',
                            'summary'=>isset($pkg[3]) ? $pkg[3] : '',
                        );
                    }
                }
            }
        }

        return $packages;
    }
}
