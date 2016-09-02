<?php
/**
* Plugin Name: Social
* Plugin URI:  http://plugins.sygnoos.com/wordpress-social-buttons/
* Description: Social media share buttons.
* Version:     1.3.8
* Author:      Sygnoos
* Author URI:  https://www.sygnoos.com
*/
if (!defined( 'ABSPATH' )) exit;
require_once(dirname(__FILE__).'/config.php');
require_once(SGMB_CLASSES.'SGMB.php');
$sgmb = new SGMB();
$sgmb->init();
