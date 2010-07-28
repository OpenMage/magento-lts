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
 * @category   Mage
 * @package    Mage_Connect
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

include_once "Maged/Connect.php";

/**
 * Class for initialize Mage_Connect lib
 *
 * @category   Mage
 * @package    Mage_Connect
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Maged_Model_Connect extends Maged_Model
{

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Retrive object of Maged_Connect
     *
     * @return Maged_Connect
     */
    public function connect()
    {
        return Maged_Connect::getInstance();
    }

    /**
     * Install All Magento
     *
     * @param boolean $force
     */
    public function installAll($force=false, $chanName='')
    {
        $options = array();
        if ($force) {
            $this->connect()->cleanSconfig();
            $options['force'] = 1;
        }
        $packages = array(
            'Mage_All_Latest',
        );
        $ftp = $this->controller()->config()->get('ftp');
        if(!empty($ftp))
            $options['ftp'] = $ftp;
        $params = array();
        $connectConfig = $this->connect()->getConfig();
        $uri="connect20.magentocommerce.com/community";
        $connectConfig->root_channel = $chanName;        
        foreach ($packages as $package) {
            $params[] = $uri;
            $params[] = $package;
        }
        $this->connect()->runHtmlConsole(array('command'=>'install', 'options'=>$options, 'params'=>$params));
    }

    /**
     * Retrieve all installed packages
     *
     * @return array
     */
    public function getAllInstalledPackages()
    {
        $connect = $this->connect();
        $sconfig = $connect->getSingleConfig();
        $connect->run('list-installed');
        $output = $connect->getOutput();
        $packages = array();
        if (is_array($output) && isset($output['list-installed']['data'])){
            $packages = $output['list-installed']['data'];
        } else {

        }
        foreach ($packages as $channel=>$package) {
            foreach ($package as $name=>$data) {
                $summary = $sconfig->getPackageObject($channel, $name)->getSummary();
                $addition = array('summary'=>$summary, 'upgrade_versions'=>array(), 'upgrade_latest'=>'');
                $packages[$channel][$name] = array_merge($data, $addition);
            }
        }

        if (!empty($_GET['updates'])) {
            $result = $connect->run('list-upgrades');
            $output = $connect->getOutput();
            if (is_array($output)) {
                $channelData = $output;
                if (!empty($channelData['list-upgrades']['data']) && is_array($channelData['list-upgrades']['data'])) {
                    foreach ($channelData['list-upgrades']['data'] as $channel=>$package) {
                        foreach ($package as $name=>$data) {
                            if (!isset($packages[$channel][$name])) {
                                continue;
                            }
                            $packages[$channel][$name]['upgrade_latest'] = $data['to'].' ('.$data['from'].')';
                        }
                    }
                }
            }
        }

        $states = array('snapshot'=>0, 'devel'=>1, 'alpha'=>2, 'beta'=>3, 'stable'=>4);
        $preferredState = $states[$this->getPreferredState()];

        foreach ($packages as $channel=>&$package) {
            foreach ($package as $name=>&$data) {
                $actions = array();
                $systemPkg = $name==='Mage_Downloader';
                if (!empty($data['upgrade_latest'])) {
                    $status = 'upgrade-available';
                    $releases = array();
                    $connect->run('info', array(), array($channel, $name));
                    $output = $connect->getOutput();
                    if (!empty($output['info']['releases'])) {
                        foreach ($output['info']['releases'] as $release) {
                            if ($states[$release['s']]<min($preferredState, $states[$packages[$channel][$name]['stability']])) {
                                continue;
                            }
                            if (version_compare($version, $packages[$channel][$name]['version'])<1) {
                                continue;
                            }
                            $releases[$version] = $version.' ('.$release['state'].')';
                        }
                    }

                    if ($releases) {
                        uksort($releases, 'version_compare');
                        foreach ($releases as $release) {
                            $actions['upgrade|'.$release['v']] = 'Upgrade to '.$release['v'];
                        }
                    } else {
                        $a = explode(' ', $data['upgrade_latest'], 2);
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
                $packages[$channel][$name]['actions'] = $actions;
                $packages[$channel][$name]['status'] = $status;
            }
        }
        return $packages;
    }

    /**
     * Run packages action
     *
     * @param mixed $packages
     */
    public function applyPackagesActions($packages, $ignoreLocalModification='')
    {
        $actions = array();
        foreach ($packages as $package=>$action) {
            if ($action) {
                $a = explode('|', $package);
                $b = explode('|', $action);
                $package = $a[1];
                $channel = $a[0];
                $version = '';
                if ($b[0]=='upgrade') {
                    $version = $b[1];
                }
                $actions[$b[0]][] = array($channel, $package, $version, $version);
            }
        }
        if (empty($actions)) {
            $this->connect()->runHtmlConsole('No actions selected');
            exit;
        }

        $this->controller()->startInstall();

        $options = array();
        if (!empty($ignoreLocalModification)) {
            $options = array('ignorelocalmodification'=>1);
        }
        if(!$this->controller()->isWritable()||strlen($this->controller()->config()->get('ftp'))>0){
            $options['ftp'] = $this->controller()->config()->get('ftp');
        }

        foreach ($actions as $action=>$packages) {
            foreach ($packages as $package) {
                switch ($action) {
                    case 'install': case 'uninstall': case 'upgrade':
                        $this->connect()->runHtmlConsole(array(
                            'command'=>$action,
                            'options'=>$options,
                            'params'=>$package
                        ));
                        break;

                    case 'reinstall':
                        $this->connect()->runHtmlConsole(array(
                            'command'=>'install',
                            'options'=>array('force'=>1),
                            'params'=>$package
                        ));
                        break;
                }
            }
        }

        $this->controller()->endInstall();
    }

    
    public function installUploadedPackage($file)
    {
        $this->controller()->startInstall();

        $options = array();
        if(!$this->controller()->isWritable()||strlen($this->controller()->config()->get('ftp'))>0){
            $options['ftp'] = $this->controller()->config()->get('ftp');
        }
        $this->connect()->runHtmlConsole(array(
            'command'=>'install-file',
            'options'=>$options,
            'params'=>array($file),
        ));
        $this->controller()->endInstall();
    }
    
    /**
     * Install package by id
     *
     * @param string $id
     * @param boolean $force
     */
    public function installPackage($id, $force=false)
    {
        $match = array();
        //if (!preg_match('#^([^ ]+) ([^-]+)(-[^-]+)?$#', $id, $match)) {// there is bug? space not used as separator "/" must be there. Version number part (-[^-]+) may be optional?
        //if (!preg_match('#^([^\/]+)\/([^-]+)?$#', $id, $match)&&!preg_match('#^([^ ]+)\/([^-]+)(-[^-]+)?$#', $id, $match)&&!preg_match('#^([^ ]+) ([^-]+)(-[^-]+)?$#', $id, $match)) {
        if (!preg_match('#^([^ ]+)\/([^-]+)(-[^-]+)?$#', $id, $match)) {
            $this->connect()->runHtmlConsole('Invalid package identifier provided: '.$id);
            exit;
        }

        $channel = $match[1];
        $package = $match[2];//.(!empty($match[3]) ? $match[3] : '');
        $version = (!empty($match[3]) ? trim($match[3],'/\-') : '');

        $this->controller()->startInstall();

        $options = array();
        if ($force) {
            $options['force'] = 1;
        }
        if(!$this->controller()->isWritable()||strlen($this->controller()->config()->get('ftp'))>0){
            $options['ftp'] = $this->controller()->config()->get('ftp');
        }

        $this->connect()->runHtmlConsole(array(
            'command'=>'install',
            'options'=>$options,
            'params'=>array(0=>$channel, 1=>$package, 2=>$version),
        ));

        $this->controller()->endInstall();
    }

    /**
     * Retrieve stability choosen client
     *
     * @return string alpha, beta, ...
     */
    public function getPreferredState()
    {
        if (is_null($this->get('preferred_state'))) {
            $connectConfig = $this->connect()->getConfig();
            $this->set('preferred_state', $connectConfig->__get('preferred_state'));
        }
        return $this->get('preferred_state');
    }

    /**
     * Retrieve protocol choosen client
     *
     * @return string http, ftp
     */
    public function getProtocol()
    {
        if (is_null($this->get('protocol'))) {
            $connectConfig = $this->connect()->getConfig();
            $this->set('protocol', $connectConfig->__get('protocol'));
        }
        return $this->get('protocol');
    }

    /**
     * Save settings.
     *
     * @param array $p
     */
    public function saveConfigPost($p)
    {
        $configObj=$this->connect()->getConfig();
        if('ftp' == $p['deployment_type'] || '1' == $p['inst_protocol']){
            $this->set('ftp',$p['ftp']);
        }else{
            $p['ftp']='';
        }
        $configObj->remote_config=$p['ftp'];
        $configObj->preferred_state = $p['preferred_state'];
        $configObj->protocol = $p['protocol'];
        if('1'==$p['use_custom_permissions_mode']){
            $configObj->global_dir_mode=octdec(intval($p['mkdir_mode']));
            $configObj->global_file_mode=octdec(intval($p['chmod_file_mode']));
        }
        $this->controller()->session()->addMessage('success', 'Settings has been successfully saved');
        return $this;
    }

}
