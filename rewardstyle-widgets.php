<?php
use \RewardStyle;

/*
Plugin Name: rewardStyle Widget
Plugin URI: https://www.rewardstyle.com
Description: The rewardStyle plugin allows influencers to use rewardStyle widgets on their WordPress blog
Author: rewardStyle
Author URI: https://www.rewardstyle.com
Version: 1.6
*/

require 'plugin-update-checker/plugin-update-checker.php';
require 'classes/RewardStyleWidgets.class.php';

new RewardStyle\RewardStyleWidgets();

$ExampleUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://assets.rewardstyle.com/assets/info.json',
    __FILE__,
    'rewardstyle-widgets',
    1
);

/**
 * Add these to the KSES 'Allowed Post Tags' global
 * var. Keeps these tags from being removed in the
 * save/update process.
 */
$GLOBALS['allowedposttags']['iframe'] = array(
    'id'           => TRUE,
    'class'        => TRUE,
    'title'        => TRUE,
    'style'        => TRUE,
    'align'        => TRUE,
    'frameborder'  => TRUE,
    'height'       => TRUE,
    'longdesc'     => TRUE,
    'marginheight' => TRUE,
    'marginwidth'  => TRUE,
    'name'         => TRUE,
    'scrolling'    => TRUE,
    'src'          => TRUE,
    'width'        => TRUE
);
$GLOBALS['allowedposttags']['script'] = array(
    'id'    => TRUE,
    'class' => TRUE,
    'src'   => TRUE,
    'type'  => TRUE,
    'name'  => TRUE
);

/**
 * Add these to the Tiny MCE whitelist of acceptable Tags.
 * This keeps the values available when loading the page,
 * and when switching from Visual/Text Tabs
 */
function unfilter_iframe($initArray) {
  $initArray['extended_valid_elements'] = "+iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]";
  if (isset($initArray['extended_valid_elements'])) {
    $initArray['extended_valid_elements'] .= ",+iframe[id|class|title|style|align|frameborder|height|longdesc|marginheight|marginwidth|name|scrolling|src|width]";
  }

  return $initArray;
}

function unfilter_script($initArray) {
  $initArray['extended_valid_elements'] = "+script[id|class|src|type|name]";
  if (isset($initArray['extended_valid_elements'])) {
    $initArray['extended_valid_elements'] .= ",+script[id|class|src|type|name]";
  }

  return $initArray;
}
add_filter('tiny_mce_before_init', 'unfilter_iframe');
add_filter('tiny_mce_before_init', 'unfilter_script');
add_filter('widget_text', 'do_shortcode');
add_filter('the_excerpt', 'do_shortcode');
