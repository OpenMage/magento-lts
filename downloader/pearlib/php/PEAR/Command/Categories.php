<?php
/**
 * PEAR_Command_Categories (list-packages, list-categories, list-category
 * commands)
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR_Frontend_Web
 * @author     Tias Guns <tias@ulyssis.org>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id: Categories.php,v 1.1 2007/06/17 14:33:52 tias Exp $
 * @link       http://pear.php.net/package/PEAR_Frontend_Web
 * @since      File available since Release 1.0
 */

/**
 * base class
 */
require_once 'PEAR/Command/Common.php';
require_once 'PEAR/REST.php';

/**
 * PEAR_Frontend_Web command for listing individual category information
 *
 * @category   pear
 * @package    PEAR_Frontend_Web
 * @author     Tias Guns <tias@ulyssis.org>
 * @copyright  1997-2007 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: TODO
 * @link       http://pear.php.net/package/PEAR_Frontend_Web
 * @since      File available since Release 0.6.0
 */
class PEAR_Command_Categories extends PEAR_Command_Common
{
    // {{{ properties

    var $commands = array(
        'list-packages' => array(
            'summary' => 'List All Packages of a Channel',
            'function' => 'doListPackages',
            'shortcut' => 'lp',
            'options' => array(
                'channel' => array(
                    'shortopt' => 'c',
                    'doc' => 'specify a channel other than the default channel',
                    'arg' => 'CHAN',
                    ),
                'allchannels' => array(
                    'shortopt' => 'a',
                    'doc' => 'list available packages from all channels',
                    ),
                ),
            'doc' => '
Lists all the packages of a channel. For each channel it displays the
channel and package name.',
            ),
        'list-categories' => array(
            'summary' => 'List All Categories',
            'function' => 'doListCategories',
            'shortcut' => 'cats',
            'options' => array(
                'channel' => array(
                    'shortopt' => 'c',
                    'doc' => 'specify a channel other than the default channel',
                    'arg' => 'CHAN',
                    ),
                'allchannels' => array(
                    'shortopt' => 'a',
                    'doc' => 'list available categories from all channels',
                    ),
                'packages' => array(
                    'shortopt' => 'p',
                    'doc' => 'list the packagenames of the categories too',
                    ),
                ),
            'doc' => '
Lists the categories available on the channel server. For each channel
it displays the channel and categorie name, and optionally the all the
names of the packages in the categories.',
            ),
        'list-category' => array(
            'summary' => 'List All Packages of a Category',
            'function' => 'doListCategory',
            'shortcut' => 'cat',
            'options' => array(
                'channel' => array(
                    'shortopt' => 'c',
                    'doc' => 'specify a channel other than the default channel',
                    'arg' => 'CHAN',
                    )
                ),
            'doc' => '<category> [<category>...]
Lists all the packages of a category of a channel. For each category
it displays the channel and package name, with local and remote version
information, and the summary.',
            ),
        );

    // }}}
    // {{{ constructor

    /**
     * PEAR_Command_Registry constructor.
     *
     * @access public
     */
    function PEAR_Command_Categories(&$ui, &$config)
    {
        parent::PEAR_Command_Common($ui, $config);
    }

    // }}}
    // {{{ doListPackages()

    function doListPackages($command, $options, $params)
    {
        $reg = &$this->config->getRegistry();
        if (isset($options['allchannels']) && $options['allchannels'] == true) {
            // over all channels
            unset($options['allchannels']);
            $channels = $reg->getChannels();
            $errors = array();
            foreach ($channels as $channel) {
                if ($channel->getName() != '__uri') {
                    $options['channel'] = $channel->getName();
                    $ret = $this->doListPackages($command, $options, $params);
                    if ($ret !== true) {
                        $errors[] = $ret;
                    }
                }
            }
            if (count($errors) !== 0) {
                // for now, only give first error
                return $errors[0];
            }
            return true;
        }

        $savechannel = $channel = $this->config->get('default_channel');
        if (isset($options['channel'])) {
            $channel = $options['channel'];
            if ($reg->channelExists($channel)) {
                $this->config->set('default_channel', $channel);
            } else {
                return $this->raiseError("Channel \"$channel\" does not exist");
            }
        }
        $chan = $reg->getChannel($channel);
        // we need Remote::_checkChannelForStatus()
        require_once 'PEAR/Command/Remote.php';
        //$cmd = new PEAR_Command_Remote($this->ui, $this->config);
        //if (PEAR::isError($e = $cmd->_checkChannelForStatus($channel, $chan))) {
        if (PEAR::isError($e = PEAR_Command_Remote::_checkChannelForStatus($channel, $chan))) {
            return $e;
        }
        if ($chan->supportsREST($this->config->get('preferred_mirror')) &&
              $base = $chan->getBaseURL('REST1.0', $this->config->get('preferred_mirror'))) {
            $rest = &$this->config->getREST('1.0', array());
            $packages = $rest->listPackages($base);
        } else {
            return PEAR::raiseError($command.' only works for REST servers');
        }
        if (PEAR::isError($packages)) {
            $this->config->set('default_channel', $savechannel);
            return $this->raiseError('The package list could not be fetched from the remote server. Please try again. (Debug info: "' . $packages->getMessage() . '")');
        }

        $data = array(
            'caption' => 'Channel ' . $channel . ' All packages:',
            'border' => true,
            'headline' => array('Channel', 'Package'),
            'channel' => $channel,
            );

        if (count($packages) === 0) {
            unset($data['headline']);
            $data['data'] = 'No packages registered';
        } else {
            $data['data'] = array();
            foreach($packages as $item) {
                $array = array(
                        $channel,
                        $item,
                            );
                $data['data'][] = $array;
            }
        }

        $this->config->set('default_channel', $savechannel);
        $this->ui->outputData($data, $command);
        return true;
    }

    // }}}
    // {{{ doListCategories()

    function doListCategories($command, $options, $params)
    {
        $reg = &$this->config->getRegistry();
        if (isset($options['allchannels']) && $options['allchannels'] == true) {
            // over all channels
            unset($options['allchannels']);
            $channels = $reg->getChannels();
            $errors = array();
            foreach ($channels as $channel) {
                if ($channel->getName() != '__uri') {
                    $options['channel'] = $channel->getName();
                    $ret = $this->doListCategories($command, $options, $params);
                    if ($ret !== true) {
                        $errors[] = $ret;
                    }
                }
            }
            if (count($errors) !== 0) {
                // for now, only give first error
                return $errors[0];
            }
            return true;
        }

        $savechannel = $channel = $this->config->get('default_channel');
        if (isset($options['channel'])) {
            $channel = $options['channel'];
            if ($reg->channelExists($channel)) {
                $this->config->set('default_channel', $channel);
            } else {
                return $this->raiseError("Channel \"$channel\" does not exist");
            }
        }
        $chan = $reg->getChannel($channel);
        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        // we need Remote::_checkChannelForStatus()
        require_once 'PEAR/Command/Remote.php';
        //$cmd = new PEAR_Command_Remote($this->ui, $this->config);
        //if (PEAR::isError($e = $cmd->_checkChannelForStatus($channel, $chan))) {
        if (PEAR::isError($e = PEAR_Command_Remote::_checkChannelForStatus($channel, $chan))) {
            return $e;
        }
        if ($chan->supportsREST($this->config->get('preferred_mirror')) &&
              $base = $chan->getBaseURL('REST1.1', $this->config->get('preferred_mirror'))) {
            $rest = &$this->config->getREST('1.1', array());
        } elseif ($chan->supportsREST($this->config->get('preferred_mirror')) &&
              $base = $chan->getBaseURL('REST1.0', $this->config->get('preferred_mirror'))) {
            $rest = &$this->config->getREST('1.0', array());
        } else {
            return PEAR::raiseError($command.' only works for REST servers');
        }
        $categories = $rest->listCategories($base);

        $data = array(
            'caption' => 'Channel ' . $channel . ' All categories:',
            'border' => true,
            'headline' => array('Channel', 'Category'),
            'channel' => $channel,
            );
        if (isset($options['packages']) && $options['packages']) {
            $data['headline'][] = 'Packages';
        }

        if (PEAR::isError($categories)) {
            unset($data['headline']);
            $data['data'] = 'The category list could not be fetched from the remote server. Please try again. (Debug info: "' . $categories->getMessage() . '")';
        } elseif (count($categories) === 0) {
            unset($data['headline']);
            $data['data'] = 'No categories registered';
        } else {
            $data['data'] = array();

            foreach($categories as $item) {
                $category = $item['_content'];
                $array = array(
                        $channel,
                        $category);

                if (isset($options['packages']) && $options['packages']) {
                    // get packagenames
                    $cat_pkgs = $rest->listCategory($base, $category);
                    if (!PEAR::isError($cat_pkgs)) {
                        $packages = array();
                        foreach($cat_pkgs as $cat_pkg) {
                            $packages[] = $cat_pkg['_content'];
                        }
                        $array[] = $packages;
                    }
                }
                $data['data'][] = $array;
            }
        }
        PEAR::staticPopErrorHandling();

        $this->config->set('default_channel', $savechannel);
        $this->ui->outputData($data, $command);
        return true;
    }

    // }}}
    // {{{ doListCategory()

    function doListCategory($command, $options, $params)
    {
        if (count($params) < 1) {
            return PEAR::raiseError('Not enough parameters, use: '.$command.' <category> [<category>...]');
        }
        if (count($params) > 1) {
            $errors = array();
            foreach($params as $pkg) {
                $ret = $this->doListCategory($command, $options, array($pkg));
                if ($ret !== true) {
                    $errors[] = $ret;
                    return $ret;
                }
            }
            if (count($errors) !== 0) {
                // for now, only give first error
                return $errors[0];
            }
            return true;
        }
        $category = $params[0];
            
        $savechannel = $channel = $this->config->get('default_channel');
        $reg = &$this->config->getRegistry();
        if (isset($options['channel'])) {
            $channel = $options['channel'];
            if ($reg->channelExists($channel)) {
                $this->config->set('default_channel', $channel);
            } else {
                return $this->raiseError("Channel \"$channel\" does not exist");
            }
        }
        $chan = $reg->getChannel($channel);
        // we need Remote::_checkChannelForStatus()
        require_once 'PEAR/Command/Remote.php';
        //$cmd = new PEAR_Command_Remote($this->ui, $this->config);
        //if (PEAR::isError($e = $cmd->_checkChannelForStatus($channel, $chan))) {
        if (PEAR::isError($e = PEAR_Command_Remote::_checkChannelForStatus($channel, $chan))) {
            return $e;
        }
        if ($chan->supportsREST($this->config->get('preferred_mirror')) &&
              $base = $chan->getBaseURL('REST1.1', $this->config->get('preferred_mirror'))) {
            $rest = &$this->config->getREST('1.1', array());
        } elseif ($chan->supportsREST($this->config->get('preferred_mirror')) &&
              $base = $chan->getBaseURL('REST1.0', $this->config->get('preferred_mirror'))) {
            $rest = &$this->config->getREST('1.0', array());
        } else {
            return PEAR::raiseError($command.' only works for REST servers');
        }
        $packages = $rest->listCategory($base, $category, true);
        if (PEAR::isError($packages)) {
            $this->config->set('default_channel', $savechannel);
            return $this->raiseError('The package list could not be fetched from the remote server. Please try again. (Debug info: "' . $packages->getMessage() . '")');
        }

        $data = array(
            'caption' => 'Channel '.$channel.' Category '.$category.' All packages:',
            'border' => true,
            'headline' => array('Channel', 'Package', 'Local', 'Remote', 'Summary'),
            'channel' => $channel,
            );
        if (count($packages) === 0) {
            unset($data['headline']);
            $data['data'] = 'No packages registered';
        } else {
            $data['data'] = array();
            foreach ($packages as $package_data) {
                $package = $package_data['_content'];
                $info = $package_data['info'];
                if (!isset($info['v'])) {
                    $remote = '-';
                } else {
                    $remote = $info['v'].' ('.$info['st'].')';
                }
                $summary = $info['s'];
                if ($reg->packageExists($package, $channel)) {
                    $local = sprintf('%s (%s)',
                        $reg->packageInfo($package, 'version', $channel),
                        $reg->packageInfo($package, 'release_state', $channel));
                } else {
                    $local = '-';
                }
                $data['data'][] = array($channel, $package, $local, $remote, $summary);
            }
        }

        $this->config->set('default_channel', $savechannel);
        $this->ui->outputData($data, $command);
        return true;
    }

}

?>
