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
 * @category   Varien
 * @package    Varien_Object
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

include_once "Maged/Pear.php";

class Maged_Model_Pear extends Maged_Model
{
    protected $_remotePackages;

    protected function _construct()
    {
        parent::_construct();
    }

    public function pear()
    {
        return Maged_Pear::getInstance();
    }

    public function installAll($force=false)
    {
        $options = array();
        if ($force) {
            $this->pear()->cleanRegistry();
            $options['force'] = 1;
        }
        $packages = array(
            'Mage_All_Latest',
        );
        $params = array();
        foreach ($packages as $pkg) {
            $params[] = 'connect.magentocommerce.com/core/'.$pkg;
        }
        $this->pear()->runHtmlConsole(array('command'=>'install', 'options'=>$options, 'params'=>$params));
    }

    public function upgradeAll()
    {
        $this->pear()->runHtmlConsole(array('command'=>'upgrade-all'));
    }

    public function getAllPackages()
    {
        $pear = $this->pear();

        $packages = array();

        $reg = $pear->getRegistry();

        foreach ($this->pear()->getMagentoChannels() as $channel=>$channelName) {
            $pear->run('list', array('channel'=>$channel));
            $output = $pear->getOutput();
            if (empty($output)) {
                continue;
            }
            foreach ($output as $channelData) {
                $channelData = $channelData['output'];
                $channel = $channelData['channel'];
                if (!is_array($channelData) || !isset($channelData['headline']) || !isset($channelData['data'])) {
                    continue;
                }
                foreach ($channelData['data'] as $pkg) {
                    $packages[$channel][$pkg[0]] = array(
                        'local_version' => $pkg[1],
                        'local_state' => $pkg[2],
                        'upgrade_versions'=>array(),
                        'upgrade_latest'=>'',
                        'summary'=>$reg->packageInfo($pkg[0], 'summary', $channel),
                    );
                }
            }
        }

        if (!empty($_GET['updates'])) {
            foreach ($this->pear()->getMagentoChannels() as $channel=>$channelName) {
                $pear->getFrontend()->clear();
                $result = $pear->run('list-upgrades', array('channel'=>$channel));
                $output = $pear->getOutput();

                if (empty($output)) {
                    continue;
                }

                foreach ($output as $channelData) {
                    if (empty($channelData['output']['data']) || !is_array($channelData['output']['data'])) {
                        continue;
                    }
                    foreach ($channelData['output']['data'] as $pkg) {
                        $pkgName = $pkg[1];
                        if (!isset($packages[$channel][$pkgName])) {
                            continue;
                        }
                        $packages[$channel][$pkgName]['upgrade_latest'] = $pkg[3].' ('.$pkg[4].')';
                    }
                }
            }
        }
        $states = array('snapshot'=>0, 'devel'=>1, 'alpha'=>2, 'beta'=>3, 'stable'=>4);
        $preferredState = $states[$this->getPreferredState()];

        foreach ($packages as $channel=>&$pkgs) {
            foreach ($pkgs as $pkgName=>&$pkg) {
                if ($pkgName=='Mage_Pear_Helpers') {
                    unset($packages[$channel][$pkgName]);
                    continue;
                }
                $actions = array();
                $systemPkg = $channel==='connect.magentocommerce.com/core' && $pkgName==='Mage_Downloader';

                if (!empty($pkg['upgrade_latest'])) {
                    $status = 'upgrade-available';

                    $releases = array();
                    $pear->getFrontend()->clear();

                    if ($pear->run('remote-info', array(), array($channel.'/'.$pkgName))) {
                        $output = $pear->getOutput();
                        if (!empty($output[0]['output']['releases'])) {
                            foreach ($output[0]['output']['releases'] as $version=>$release) {
                                if ($states[$release['state']]<min($preferredState, $states[$packages[$channel][$pkgName]['local_state']])) {
                                    continue;
                                }
                                if (version_compare($version, $packages[$channel][$pkgName]['local_version'])<1) {
                                    continue;
                                }
                                $releases[$version] = $version.' ('.$release['state'].')';
                            }
                        }
                    }
                    if ($releases) {
                        uksort($releases, 'version_compare');
                        foreach ($releases as $v=>$l) {
                            $actions['upgrade|'.$v] = 'Upgrade to '.$l;
                        }
                    } else {
                        $a = explode(' ', $pkg['upgrade_latest'], 2);
                        $actions['upgrade|'.$a[0]] = 'Upgrade';
                    }
                    if (!$systemPkg) {
                        $actions['uninstall'] = 'Uninstall';
                    }
                } else {
                    $status = 'installed';
                    $actions['reinstall'] = 'Reinstall';
                    if (!$systemPkg) {
                        $actions['uninstall'] = 'Uninstall';
                    }
                }
                $packages[$channel][$pkgName]['actions'] = $actions;
                $packages[$channel][$pkgName]['status'] = $status;
            }
        }

        return $packages;
    }

    public function applyPackagesActions($packages)
    {
        $actions = array();
        foreach ($packages as $package=>$action) {
            if ($action) {
                $a = explode('|', $package);
                $b = explode('|', $action);
                $package = $a[0].'/'.$a[1];
                if ($b[0]=='upgrade') {
                    $package .= '-'.$b[1];
                }
                $actions[$b[0]][] = $package;
            }
        }

        if (empty($actions)) {
            $this->pear()->runHtmlConsole('No actions selected');
            exit;
        }

        $this->controller()->startInstall();

        foreach ($actions as $action=>$packages) {
            switch ($action) {
                case 'install': case 'uninstall': case 'upgrade':
                    $this->pear()->runHtmlConsole(array(
                        'command'=>$action,
                        'params'=>$packages
                    ));
                    break;

                case 'reinstall':
                    $this->pear()->runHtmlConsole(array(
                        'command'=>'install',
                        'options'=>array('force'=>1),
                        'params'=>$packages
                    ));
                    break;
            }
        }

        $this->controller()->endInstall();
    }

    public function installPackage($id, $force=false)
    {
        $match = array();
        if (!preg_match('#^magento-([^/]+)/([^-]+)(-[^-]+)?$#', $id, $match)) {
            $this->pear()->runHtmlConsole('Invalid package identifier provided: '.$id);
            exit;
        }

        $pkg = 'connect.magentocommerce.com/'.$match[1].'/'.$match[2].(!empty($match[3]) ? $match[3] : '');

        $this->controller()->startInstall();

        $options = array();
        if ($force) {
            $options['force'] = 1;
        }

        $this->pear()->runHtmlConsole(array(
            'command'=>'install',
            'options'=>$options,
            'params'=>array($pkg),
        ));

        $this->controller()->endInstall();
    }

    public function getDistConfig()
    {
        if (is_null($this->get('dist_config'))) {
            $file = @file_get_contents('dist_config.xml');
            if (!$file) {
                throw new Exception('Could not load versions remote config.');
            }
            $this->set('dist_config', new SimpleXMLElement($file));
        }
        return $this->get('dist_config');
    }

    public function getDistCurrent()
    {
        if (is_null($this->get('dist_current'))) {
            $pear = $this->pear();
            foreach ($this->getDistConfig()->distributions->distribution as $dist) {
                $pear->getFrontend()->clear();
                $result = $pear->run('info', array(), array('magento-core/'.(string)$dist->metapackage));
                $output = $pear->getFrontend()->getOutput();
                if (!$output) {
                    continue;
                }
                $this->set('dist_current', $dist);
            }
        }
        return $this->get('dist_current');
    }

    public function getDistAvailable()
    {
        if (is_null($this->get('dist_available'))) {
            $states = array('devel'=>0, 'alpha'=>1, 'beta'=>2, 'stable'=>3);
            $state = $this->getPreferredState();
            $versions = array();
            $current = (string)$this->getDistCurrent()->version;
            foreach ($this->getDistConfig()->distributions->distribution as $dist) {
                if (version_compare((string)$dist->version, $current)<1) {
                    continue;
                }
                if ($states[(string)$dist->state]<$states[$state]) {
                    continue;
                }
                $versions[(string)$dist->version] = $dist;
            }
            $this->set('dist_available', $versions);
        }
        return $this->get('dist_available');
    }

    public function getDistByVersion($version)
    {
        foreach ($this->getDistConfig()->distributions->distribution as $dist) {
            if ((string)$dist->version==$version) {
                return $dist;
            }
        }
        return false;
    }

    public function getPreferredState()
    {
        if (is_null($this->get('preferred_state'))) {
            $pearConfig = $this->pear()->getConfig();
            $this->set('preferred_state', $pearConfig->get('preferred_state'));
        }
        return $this->get('preferred_state');
    }

    public function distUpgrade($version)
    {
        $dist = $this->getDistByVersion($version);
        if (!$dist) {
            echo "Invalid version.";
            return;
        }
        $packages = array('magento-core/'.(string)$this->getDistCurrent()->metapackage);
        foreach ($this->getDistCurrent()->pkgs->pkg as $pkg) {
            $packages[] = 'magento-core/'.(string)$pkg;
        }
        $result = $this->pear()->runHtmlConsole(array(
            'command'=>'uninstall',
            'options'=>array(),
            'params'=>$packages,
            'no-footer'=>true,
        ));

        if (!$result instanceof PEAR_Error) {
            $this->pear()->runHtmlConsole(array(
                'command'=>'install',
                'options'=>array(),
                'params'=>array('magento-core/'.(string)$dist->metapackage),
                'no-header'=>true,
            ));
        }

        try {
            Mage::app()->cleanAllSessions();
            Mage::app()->cleanCache();
        } catch (Exception $e) {
            $this->session()->addMessage('error', "Exception during cache and session cleaning: ".$e->getMessage());
        }
    }

    public function saveConfigPost($p)
    {
        $result = $this->pear()->run('config-set', array(), array('preferred_state', $p['preferred_state']));
        if ($result) {
            $this->controller()->session()->addMessage('success', 'Settings has been successfully saved');
        }
        return $this;
    }
}
