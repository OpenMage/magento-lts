<?php
/**
 * @category   Pear
 * @package    PEAR_Frontend_Web
 * @author     Tias Guns <tias@ulyssis.org>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @version    CVS: $Id: Docviewer.php,v 1.2 2007/05/19 15:36:40 tias Exp $
 * @link       http://pear.php.net/package/PEAR_Frontend_Web
 * @since      File available since Release 0.6.2
 */

/**
 * PEAR_Frontend_Web_Docviewer allows you to view the documentation
 * of the installed packages, in the webfrontend.
 *
 * Most packages provide some documentation files, this class allows
 * you to find out which ones, and to display there content.
 *
 * @category   Pear
 * @package    PEAR_Frontend_Web
 * @author     Tias Guns <tias@ulyssis.org>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @version    CVS: $Id: Docviewer.php,v 1.2 2007/05/19 15:36:40 tias Exp $
 * @link       http://pear.php.net/package/PEAR_Frontend_Web
 * @since      File available since Release 0.6.2
 */
class PEAR_Frontend_Web_Docviewer
{

    /**
     * The config object
     */
    var $config;

    /**
     * User Interface object, for all interaction with the user.
     * @var object
     */
    var $ui;

    /**
     * Create instance and set config to global frontweb config
     *
     * @param $ui User Interface object
     */
    function PEAR_Frontend_Web_Docviewer(&$ui)
    {
        $this->config = &$GLOBALS['_PEAR_Frontend_Web_config'];
        $this->ui = &$ui;
    }

    /**
     * Set the config, manually
     *
     * @param $config config object
     */
    function setConfig(&$config)
    {
        $this->config = &$config;
    }

    /**
     * Get the files with role 'doc' of the given package
     *
     * Can be called as static method
     *
     * @param string $package package name
     * @param string $channel
     * @return array('name' => 'installed_as', ...
     */
    function getDocFiles($package_name, $channel)
    {
        $reg = $this->config->getRegistry();
        $files_all = $reg->packageInfo($package_name, 'filelist', $channel);
        $files_doc = array();
        foreach($files_all as $name => $file) {
            if ($file['role'] == 'doc') {
                $files_doc[$name] = $file['installed_as'];
            }
        }
        return $files_doc;
    }

    /**
     * Output in HTML the list of docs of given package
     *
     * @param string $package package name
     * @param string $channel
     * @return true (uses the User Interface object)
     */
     function outputListDocs($package_name, $channel)
     {
        $command = 'list-docs';
        $data = array(
            'caption' => 'Package '.$channel.'/'.$package_name.', Documentation files:',
            'border' => true,
            'headline' => array('File', 'Location'),
            'channel' => $channel,
            'package' => $package_name,
            );

        $files = $this->getDocFiles($package_name, $channel);
        if (count($files) == 0) {
            $data['data'] = '(no documentation available)';
        } else {
            foreach ($files as $name => $location) {
                $data['data'][$name] = $location;
            }
        }
        $this->ui->outputData($data, $command);

        return true;
     }

    /**
     * Output in HTML the documentation file of given package
     *
     * @param string $package package name
     * @param string $channel
     * @param string $file
     * @return true (uses the User Interface object)
     */
     function outputDocShow($package_name, $channel, $file)
     {
        $this->outputListDocs($package_name, $channel);

        $command = 'doc-show';
        $data = array(
            'caption' => $channel.'/'.$package_name.' :: '.$file.':',
            'border' => true,
            'channel' => $channel,
            'package' => $package_name,
            );

        $files = $this->getDocFiles($package_name, $channel);
        if (!isset($files[$file])) {
            $data['data'] = 'File '.$file.' is not part of the documentation of this package.';
        } else {
            $data['data'] = file_get_contents($files[$file]);
            //$data['data'] = $file;
        }
        $this->ui->outputData($data, $command);

        return true;
     }

}

?>
