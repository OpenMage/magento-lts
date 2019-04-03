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

final class Mage_Connect_Command_Package
extends Mage_Connect_Command
{
    /**
     * Dependencies list
     * @var array
     */
    private $_depsList = array();

    /**
     * Releases list
     * @var array
     */
    private $_releasesList = array();

    /**
     * Package command callback
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void
     */
    public function doPackage($command, $options, $params)
    {
        $this->cleanupParams($params);

        if(count($params) < 1) {
            return $this->doError($command, "Parameters count should be >= 1");
        }

        $file = strtolower($params[0]);
        $file = realpath($file);

        if(!file_exists($file)) {
            return $this->doError($command, "File {$params[0]} doesn't exist");
        }

        try {
            $packager = new Mage_Connect_Package($file);
            $res = $packager->validate();
            if(!$res) {
                $this->doError($command, implode("\n", $packager->getErrors()));
                return;
            }
            $packager->save(dirname($file));
            $this->ui()->output('Done building package');
        } catch (Exception $e) {
            $this->doError( $command, $e->getMessage() );
        }
    }


    /**
     * Display/get dependencies
     * @param string $command
     * @param array $options
     * @param array $params
     * @return void/array
     */
    public function doPackageDependencies($command, $options, $params)
    {
        $this->cleanupParams($params);
        try {
            if(count($params) < 2) {
                return $this->doError($command, "Argument count should be >= 2");
            }

            $channel = $params[0];
            $package = $params[1];

            $argVersionMin = isset($params[3]) ? $params[3] : false;
            $argVersionMax = isset($params[2]) ? $params[2] : false;

            $ftp = empty($options['ftp']) ? false : $options['ftp'];
            $packager = $this->getPackager();
            if($ftp) {
                list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
            } else {
                $cache = $this->getSconfig();
                $config = $this->config();
            }
            $data = $packager->getDependenciesList($channel, $package, $cache, $config, $argVersionMax, $argVersionMin);
            $this->ui()->output(array($command=> array('data'=>$data['deps'], 'title'=>"Package deps for {$params[1]}: ")));

        } catch (Exception $e) {
            $this->doError($command, $e->getMessage());
        }
    }

    public function doConvert($command, $options, $params)
    {
        $this->cleanupParams($params);
        try {
            if(count($params) < 1) {
                throw new Exception("Arguments should be: source.tgz [target.tgz]");
            }
            $sourceFile = $params[0];
            $converter = new Mage_Connect_Converter();
            $targetFile = isset($params[1]) ? $params[1] : false;
            $result = $converter->convertPearToMage($sourceFile, $targetFile);
            $this->ui()->output("Saved to: ".$result);
        } catch (Exception $e) {
            $this->doError($command, $e->getMessage());
        }

    }

}
