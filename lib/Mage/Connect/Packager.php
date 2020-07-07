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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class to manipulate with packages
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Connect_Packager
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     *
     * @var Mage_Archive
     */
    protected $_archiver = null;
    protected $_http = null;



    /**
     *
     * @return Mage_Archive
     */
    public function getArchiver()
    {
        if(is_null($this->_archiver)) {
            $this->_archiver = new Mage_Archive();
        }
        return $this->_archiver;
    }

    public function getDownloader()
    {
        if(is_null($this->_http)) {
            $this->_http = Mage_HTTP_Client::getInstance();
        }
        return $this->_http;
    }


    public function getRemoteConf($ftpString)
    {
        $ftpObj = new Mage_Connect_Ftp();
        $ftpObj->connect($ftpString);
        $cfgFile = "connect.cfg";
        $cacheFile = "cache.cfg";


        $wd = $ftpObj->getcwd();

        $remoteConfigExists = $ftpObj->fileExists($cfgFile);
        $tempConfigFile = uniqid($cfgFile."_temp");
        if(!$remoteConfigExists) {
            $remoteCfg = new Mage_Connect_Config($tempConfigFile);
            $remoteCfg->store();
            $ftpObj->upload($cfgFile, $tempConfigFile);
        } else {
            $ftpObj->get($tempConfigFile, $cfgFile);
            $remoteCfg = new Mage_Connect_Config($tempConfigFile);
        }

        $ftpObj->chdir($wd);

        $remoteCacheExists = $ftpObj->fileExists($cacheFile);
        $tempCacheFile = uniqid($cacheFile."_temp");

        if(!$remoteCacheExists) {
            $remoteCache = new Mage_Connect_Singleconfig($tempCacheFile);
            $remoteCache->clear();
            $ftpObj->upload($cacheFile, $tempCacheFile);
        } else {
            $ftpObj->get($tempCacheFile, $cacheFile);
            $remoteCache = new Mage_Connect_Singleconfig($tempCacheFile);
        }
        $ftpObj->chdir($wd);
        return array($remoteCache, $remoteCfg, $ftpObj);
    }


    public function getRemoteCache($ftpString)
    {

        $ftpObj = new Mage_Connect_Ftp();
        $ftpObj->connect($ftpString);
        $remoteConfigExists = $ftpObj->fileExists("cache.cfg");
        if(!$remoteConfigExists) {
            $configFile= uniqid("temp_cachecfg_");
            $remoteCfg = new Mage_Connect_Singleconfig($configFile);
            $remoteCfg->clear();
            $ftpObj->upload("cache.cfg", $configFile);
        } else {
            $configFile = uniqid("temp_cachecfg_");
            $ftpObj->get($configFile, "cache.cfg");
            $remoteCfg = new Mage_Connect_Singleconfig($configFile);
        }
        return array($remoteCfg, $ftpObj);
    }


    public function getRemoteConfig($ftpString)
    {
        $ftpObj = new Mage_Connect_Ftp();
        $ftpObj->connect($ftpString);
        $cfgFile = "connect.cfg";

        $wd = $ftpObj->getcwd();
        $remoteConfigExists = $ftpObj->fileExists($cfgFile);
        $tempConfigFile = uniqid($cfgFile."_temp");
        if(!$remoteConfigExists) {
            $remoteCfg = new Mage_Connect_Config($tempConfigFile);
            $remoteCfg->store();
            $ftpObj->upload($cfgFile, $tempConfigFile);
        } else {
            $ftpObj->get($tempConfigFile, $cfgFile);
            $remoteCfg = new Mage_Connect_Config($tempConfigFile);
        }
        $ftpObj->chdir($wd);
        return array($remoteCfg, $ftpObj);
    }

    public function writeToRemoteCache($cache, $ftpObj)
    {
        $wd = $ftpObj->getcwd();
        $ftpObj->upload("cache.cfg", $cache->getFilename());
        @unlink($cache->getFilename());
        $ftpObj->chdir($wd);
    }

    public function writeToRemoteConfig($cache, $ftpObj)
    {
        $wd = $ftpObj->getcwd();
        $ftpObj->upload("connect.cfg", $cache->getFilename());
        @unlink($cache->getFilename());
        $ftpObj->chdir($wd);
    }

    /**
     *
     * @param $chanName
     * @param $package
     * @param Mage_Connect_Singleconfig $cacheObj
     * @param $ftp
     * @return unknown_type
     */
    public function processUninstallPackage($chanName, $package, $cacheObj, $configObj)
    {
        $package = $cacheObj->getPackageObject($chanName, $package);
        $contents = $package->getContents();

        $targetPath = rtrim($configObj->magento_root, "\\/");
        foreach($contents as $file) {
            $fileName = basename($file);
            $filePath = dirname($file);
            $dest = $targetPath . DIRECTORY_SEPARATOR . $filePath . DIRECTORY_SEPARATOR . $fileName;
            if(@file_exists($dest)) {
                //var_dump($dest);
                @unlink($dest);
            }
        }
    }

    /**
     *
     * @param $chanName
     * @param $package
     * @param Mage_Connect_Singleconfig $cacheObj
     * @param Mage_Connect_Ftp $ftp
     * @return unknown_type
     */
    public function processUninstallPackageFtp($chanName, $package, $cacheObj, $ftp)
    {
        $ftpDir = $ftp->getcwd();
        $package = $cacheObj->getPackageObject($chanName, $package);
        $contents = $package->getContents();
        foreach($contents as $file) {
            $res = $ftp->delete($file);
        }
        $ftp->chdir($ftpDir);
    }

    protected function convertFtpPath($str)
    {
        return str_replace("\\", "/", $str);
    }

    public function processInstallPackageFtp($package, $file, $configObj, $ftp)
    {
        $ftpDir = $ftp->getcwd();
        $contents = $package->getContents();
        $arc = $this->getArchiver();
        $target = dirname($file).DS.$package->getReleaseFilename();
        @mkdir($target, 0777, true);
        $mode = $configObj->global_dir_mode;
        $tar = $arc->unpack($file, $target);
        $modeFile = $configObj->global_file_mode;
        $modeDir = $configObj->global_dir_mode;
        foreach($contents as $file) {
            $fileName = basename($file);
            $filePath = $this->convertFtpPath(dirname($file));
            $source = $tar.DS.$file;
            if (file_exists($source) && is_file($source)) {
                $args = array(ltrim($file,"/"), $source);
                if($modeDir) {
                    $args[] = $modeDir;
                }
                call_user_func_array(array($ftp,'upload'), $args);
            }
        }
        $ftp->chdir($ftpDir);
        Mage_System_Dirs::rm(array("-r",$target));
    }

    /**
     * Package installation to FS
     * @param Mage_Connect_Package $package
     * @param string $file
     * @return void
     * @throws Exception
     */
    public function processInstallPackage($package, $file, $configObj)
    {
        $contents = $package->getContents();
        $arc = $this->getArchiver();
        $target = dirname($file).DS.$package->getReleaseFilename();
        @mkdir($target, 0777, true);
        $mode = $configObj->global_dir_mode;
        $tar = $arc->unpack($file, $target);
        $modeFile = $configObj->global_file_mode;
        $modeDir = $configObj->global_dir_mode;
        foreach($contents as $file) {
            $fileName = basename($file);
            $filePath = dirname($file);
            $source = $tar.DS.$file;
            $targetPath = rtrim($configObj->magento_root, "\\/");
            @mkdir($targetPath. DS . $filePath, $modeDir, true);
            $dest = $targetPath . DS . $filePath . DS . $fileName;
            if (is_file($source)) {
                @copy($source, $dest);
                if($modeFile) {
                    @chmod($dest, $modeFile);
                }
            } else {
                @mkdir($dest, $modeDir);
            }
        }
        Mage_System_Dirs::rm(array("-r",$target));
    }


    /**
     * Get local modified files
     * @param $chanName
     * @param $package
     * @param $cacheObj
     * @param $configObj
     * @return array
     */
    public function getLocalModifiedFiles($chanName, $package, $cacheObj, $configObj)
    {
        $p = $cachObj->getPackageObject($chanName, $package);
        $hashContents = $p->getHashContents();
        $listModified = array();
        foreach ($hashContents as $file=>$hash) {
            if (md5_file($configObj->magento_root . DS . $file)!==$hash) {
                $listModified[] = $file;
            }
        }
        return $listModified;
    }

    /**
     * Get remote modified files
     *
     * @param $chanName
     * @param $package
     * @param $cacheObj
     * @param Mage_Connect_Ftp $ftp
     * @return array
     */
    public function getRemoteModifiedFiles($chanName, $package, $cacheObj, $ftp)
    {
        $p = $cacheObj->getPackageObject($chanName, $package);
        $hashContents = $p->getHashContents();
        $listModified = array();
        foreach ($hashContents as $file=>$hash) {
            $localFile = uniqid("temp_remote_");
            if(!$ftp->fileExists($file)) {
                continue;
            }
            $ftp->get($localFile, $file);
            if (file_exists($localFile) && md5_file($localFile)!==$hash) {
                $listModified[] = $file;
            }
            @unlink($localFile);
        }
        return $listModified;
    }


    /**
     *
     * Get upgrades list
     *
     * @param string/array $channels
     * @param Mage_Connect_Singleconfig $cacheObject
     * @param Mage_Connect_Rest $restObj optional
     * @param bool $checkConflicts
     * @return array
     */
    public function getUpgradesList($channels, $cacheObject, $configObj, $restObj = null, $checkConflicts = false)
    {
        if(is_scalar($channels)) {
            $channels = array($channels);
        }

        if(!$restObj) {
            $restObj = new Mage_Connect_Rest();
        }

        $updates = array();
        foreach($channels as $chan) {

            if(!$cacheObject->isChannel($chan)) {
                continue;
            }
            $chanName = $cacheObject->chanName($chan);
            $localPackages = $cacheObject->getInstalledPackages($chanName);
            $localPackages = $localPackages[$chanName];

            if(!count($localPackages)) {
                continue;
            }

            $channel = $cacheObject->getChannel($chan);
            $uri = $channel[Mage_Connect_Singleconfig::K_URI];
            $restObj->setChannel($uri);
            $remotePackages = $restObj->getPackagesHashed();

            /**
             * Iterate packages of channel $chan
             */
            $state = $configObj->preferred_state ? $configObj->preferred_state : "devel";

            foreach($localPackages as $localName=>$localData) {
                if(!isset($remotePackages[$localName])) {
                    continue;
                }
                $package = $remotePackages[$localName];
                $neededToUpgrade = false;
                $remoteVersion = $localVersion = trim($localData[Mage_Connect_Singleconfig::K_VER]);
                foreach($package as $version => $s) {

                    if( $cacheObject->compareStabilities($s, $state) < 0 ) {
                        continue;
                    }

                    if(version_compare($version, $localVersion, ">")) {
                        $neededToUpgrade = true;
                        $remoteVersion = $version;
                    }

                    if($checkConflicts) {
                        $conflicts = $cacheObject->hasConflicts($chanName, $localName, $remoteVersion);
                        if(false !== $conflicts) {
                            $neededToUpgrade = false;
                        }
                    }
                }
                if(!$neededToUpgrade) {
                    continue;
                }
                if(!isset($updates[$chanName])) {
                    $updates[$chanName] = array();
                }
                $updates[$chanName][$localName] = array("from"=>$localVersion, "to"=>$remoteVersion);
            }
        }
        return $updates;
    }

    /**
     * Get uninstall list
     * @param string $chanName
     * @param string $package
     * @param Mage_Connect_Singleconfig $cache
     * @param Mage_Connect_Config $config
     * @param bool $withDepsRecursive
     * @return array
     */
    public function getUninstallList($chanName, $package, $cache, $config, $withDepsRecursive = true)
    {
        static $level = 0;
        static $hash = array();

        $chanName = $cache->chanName($chanName);
        $keyOuter = $chanName . "/" . $package;
        $level++;

        try {
            $chanName = $cache->chanName($chanName);
            if(!$cache->hasPackage($chanName, $package)) {
                $level--;
                if($level == 0) {
                    $hash = array();
                    return array('list'=>array());
                }
                return;
            }
            $dependencies = $cache->getPackageDependencies($chanName, $package);
            $data = $cache->getPackage($chanName, $package);
            $version = $data['version'];
            $keyOuter = $chanName . "/" . $package;

            //print "Processing outer: {$keyOuter} \n";
            $hash[$keyOuter] = array (
                'name'     => $package,
                'channel'  => $chanName,
                'version'  => $version,
                'packages' => $dependencies,
            );

            if($withDepsRecursive) {
                $flds = array('name','channel','min','max');
                $fldsCount = count($flds);
                foreach($dependencies as $row) {
                    foreach($flds as $key) {
                        $varName = "p".ucfirst($key);
                        $$varName = $row[$key];
                    }
                    $method = __FUNCTION__;
                    $keyInner = $pChannel . "/" . $pName;
                    if(!isset($hash[$keyInner])) {
                        $this->$method($pChannel, $pName, $cache, $config,
                        $withDepsRecursive, false);
                    }
                }
            }

        } catch (Exception $e) {
        }

        $level--;
        if(0 === $level) {
            $out = $this->processDepsHash($hash);
            $hash = array();
            return array('list'=>$out);
        }
    }

    /**
     * Get dependencies list/install order info
     *
     *
     * @param string $chanName
     * @param string $package
     * @param Mage_Connect_Singleconfig $cache
     * @param Mage_Connect_Config $config
     * @param mixed $versionMax
     * @param mixed $versionMin
     * @return mixed
     */
    public function getDependenciesList($chanName, $package, $cache, $config, $versionMax = false, $versionMin = false,
        $withDepsRecursive = true, $forceRemote = false
    ) {
        static $level = 0;
        static $_depsHash = array();
        static $_deps = array();
        static $_failed = array();

        $level++;

        try {
            $chanName = $cache->chanName($chanName);

            $rest = new Mage_Connect_Rest($config->protocol);
            $rest->setChannel($cache->chanUrl($chanName));
            $releases = $rest->getReleases($package);
            if(!$releases || !count($releases)) {
                throw new Exception("No releases for: '{$package}', skipping");
            }
            $state = $config->preffered_state ? $confg->preffered_state : 'devel';
            $version = $cache->detectVersionFromRestArray($releases, $versionMin, $versionMax, $state);
            if(!$version) {
                throw new Exception("Version for '{$package}' was not detected");
            }
            $packageInfo = $rest->getPackageReleaseInfo($package, $version);
            if(false === $packageInfo) {
                throw new Exception("Package release '{$package}' not found on server");
            }
            unset($rest);
            $dependencies = $packageInfo->getDependencyPackages();
            $keyOuter = $chanName . "/" . $package;

            //print "Processing outer: {$keyOuter} \n";
            $_depsHash[$keyOuter] = array (
                'name'               => $package,
                'channel'            => $chanName,
                'downloaded_version' => $version,
                'min'                => $versionMin,
                'max'                => $versionMax,
                'packages'           => $dependencies,
            );

            if($withDepsRecursive) {
                $flds = array('name','channel','min','max');
                $fldsCount = count($flds);
                foreach($dependencies as $row) {
                    foreach($flds as $key) {
                        $varName = "p".ucfirst($key);
                        $$varName = $row[$key];
                    }
                    $method = __FUNCTION__;
                    $keyInner = $pChannel . "/" . $pName;
                    if(!isset($_depsHash[$keyInner])) {
                        $_deps[] = $row;
                        $this->$method($pChannel, $pName, $cache, $config,
                        $pMax, $pMin, $withDepsRecursive, $forceRemote, false);
                    } else {
                        $downloaded = $_depsHash[$keyInner]['downloaded_version'];
                        $hasMin = $_depsHash[$keyInner]['min'];
                        $hasMax = $_depsHash[$keyInner]['max'];
                        if($pMin === $hasMin && $pMax === $hasMax) {
                            //var_dump("Equal requirements, skipping");
                            continue;
                        }

                        if($cache->versionInRange($downloaded, $pMin, $pMax)) {
                            //var_dump("Downloaded package matches new range too");
                            continue;
                        }

                        $names = array("pMin","pMax","hasMin","hasMax");
                        for($i=0, $c=count($names); $i<$c; $i++) {
                            if(!isset($$names[$i])) {
                                continue;
                            }
                            if(false !== $$names[$i]) {
                                continue;
                            }
                            $$names[$i] = $i % 2 == 0 ? "0" : "999999999";
                        }

                        if(!$cache->hasVersionRangeIntersect($pMin,$pMax, $hasMin, $hasMax)) {
                            $reason = "Detected {$pName} conflict of versions: {$hasMin}-{$hasMax} and {$pMin}-{$pMax}";
                            unset($_depsHash[$keyInner]);
                            $_failed[] = array(
                                'name'    => $pName,
                                'channel' => $pChannel,
                                'max'     => $pMax,
                                'min'     => $pMin,
                                'reason'  => $reason
                            );
                            continue;
                        }
                        $newMaxIsLess = version_compare($pMax, $hasMax, "<");
                        $newMinIsGreater = version_compare($pMin, $hasMin, ">");
                        $forceMax = $newMaxIsLess ? $pMax : $hasMax;
                        $forceMin = $newMinIsGreater ? $pMin : $hasMin;
                        //var_dump("Trying to process {$pName} : max {$forceMax} - min {$forceMin}");
                        $this->$method($pChannel, $pName, $cache, $config,
                        $forceMax, $forceMin, $withDepsRecursive, $forceRemote);
                    }
                }
            }
        } catch (Exception $e) {
            $_failed[] = array(
                'name'    => $package,
                'channel' => $chanName,
                'max'     => $versionMax,
                'min'     => $versionMin,
                'reason'  => $e->getMessage()
            );
        }

        $level--;
        if($level == 0) {
            $out = $this->processDepsHash($_depsHash);
            $deps = $_deps;
            $failed = $_failed;
            $_depsHash = array();
            $_deps = array();
            $_failed = array();
            return array('deps' => $deps, 'result' => $out, 'failed'=> $failed);
        }
    }

    /**
     * Process dependencies hash
     * Makes topological sorting and gives operation order list
     *
     * @param array $depsHash
     * @param bool $sortReverse
     * @return array
     */
    protected function processDepsHash(&$depsHash, $sortReverse = true)
    {
        $nodes = array();
        $graph = new Mage_Connect_Structures_Graph();

        foreach($depsHash as $key=>$data) {
            $packages = $data['packages'];
            $node = new Mage_Connect_Structures_Node();
            $nodes[$key] =& $node;
            unset($data['packages']);
            $node->setData($data);
            $graph->addNode($node);
            unset($node);
        }

        if(count($nodes) > 1) {
            foreach($depsHash as $key=>$data) {
                $packages = $data['packages'];
                foreach($packages as $pdata) {
                    $pName = $pdata['channel'] . "/" . $pdata['name'];
                    if(isset($nodes[$key], $nodes[$pName])) {
                        $nodes[$key]->connectTo($nodes[$pName]);
                    }
                }
            }
        }
        $result = $graph->topologicalSort();
        $sortReverse ? krsort($result) : ksort($result);
        $out = array();
        $total = 0;
        foreach($result as $order=>$nodes) {
            foreach($nodes as $n) {
                $out[] = $n->getData();
            }
        }
        unset($graph, $nodes);
        return $out;
    }
}
