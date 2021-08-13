<?php
namespace RewardStyle;

class RewardStyleWidgets {
  private $adblock_message  = 'Disable your ad blocking software to view this content.';
  private $enablejs_message = 'JavaScript is currently disabled in this browser. Reactivate it to view this content.';
  private $adblock_html = '';

  private function load_javascript($src, $script_id = 'reward-scripts', $tag = 'script', $defer = true) {
    return "
      !function(a,b,c,f,g){let h,i=b.location.protocol;b.getElementById(f)||(h=b.createElement(c),h.id=f,h.src=i+a,h.defer=g,b.body.appendChild(h))}('$src',document,'$tag','$script_id',$defer);
    ";
  }

  public function __construct() {
    //Add dns-prefetch
    add_action('wp_head', function() {
      echo "
        <link rel='dns-prefetch' href='//assets.rewardstyle.com'>
        <link rel='dns-prefetch' href='//widgets.rewardstyle.com'>
        <link rel='dns-prefetch' href='//images.rewardstyle.com'>
        <link rel='dns-prefetch' href='//widgets-static.rewardstyle.com'>
        <link rel='dns-prefetch' href='//product-images-cdn.liketoknow.it'>
      ";
    }, 20);

    $this->adblock_html = "<div class='rs-adblock'><img src='//assets.rewardstyle.com/images/search/350.gif' onerror='this.parentNode.innerHTML=\'$this->adblock_message\'' /><noscript>$this->enablejs_message</noscript></div>";
    // Add shortcode support to completely bypass the iframe filter
    add_shortcode('show_rs_widget', [$this, 'rs_show_widget']);
    add_shortcode('show_ms_widget', [$this, 'ms_show_widget']);
    add_shortcode('show_ms_widget', [$this, 'ltk_show_widget']);
    add_shortcode('show_ltk_widget_version_two', [$this, 'ltk_widget_version_two']);
    add_shortcode('show_lookbook_widget', [$this, 'lookbook_show_widget']);
    add_shortcode('show_shopthepost_widget', [$this, 'shopthepost_show_widget']);
    add_shortcode('show_boutique_widget', [$this, 'boutique_show_widget']);
  }

  public function rs_show_widget($atts) {
    extract(shortcode_atts(array(
      'wid'         => '',
      'blog'        => '',
      'product_ids' => '',
      'rows'        => '',
      'cols'        => '',
      'brand'       => '',
      'price'       => '',
      'hover'       => ''
    ), $atts));

    $width     = ($cols * 110) + 50;
    $height    = $rows * 120;
    $magic_num = 0;
    $how_tall  = '120';
    $prod_box  = 'show';

    if ($brand == 1) {
      $magic_num++;
    }

    if ($price == 1) {
      $magic_num++;
    }

    if ($hover == 1) {
      $magic_num = 0;
      $prod_box = 'hover-info';
    }

    if ($magic_num == 1) {
      $how_tall = '162';
    } else if ($magic_num == 2) {
      $how_tall = '195';
    }

    $out = "
      <div style='width:{$width}px; height: {$height}px; margin: 0 auto; background: #fff;'>
        <iframe loading='lazy' frameborder='0' scrolling='no' src='https://currentlyobsessed.me/api/v1/get_widget?wid={$wid}&blog={$blog}&product_ids={$product_ids}&rows={$rows}&cols={$cols}&brand={$brand}&price={$price}&hover={$hover}'></iframe>
      </div>
    ";

    return $out;
  }

  public function ms_show_widget($atts) {
    extract(shortcode_atts(array(
        'id'       => '0',
        'image_id' => '0',
        'width'    => '0',
        'height'   => '0',
        'adblock'  => $this->adblock_message,
        'enableJs' => $this->enablejs_message
    ), $atts));

    $this->adblock_message = $adblock;
    $this->enablejs_message = $enableJs;

    return "
      <div class='moneyspot-widget' data-widget-id='$id'>
        <script>
          {$this->load_javascript('//widgets.rewardstyle.com/js/widget.js', 'moneyspot-script')}'object'===typeof window.__moneyspot&&'complete'===document.readyState&&window.__moneyspot.init();
        </script>
        <div class='rs-adblock'>
          <img src='//images.rewardstyle.com/img?v=2.11&ms={$id}&aspect' onerror='this.parentNode.innerHTML=\'{$this->adblock_message}\'' />
          <noscript>'.$enableJs.'</noscript>
        </div>
      </div>
    ";
  }

  public function ltk_show_widget($atts) {
    extract(shortcode_atts(array(
      'user_id'    => '0',
      'rows'       => '1',
      'cols'       => '6',
      'show_frame' => 'true',
      'padding'    => '0'
    ), $atts));

    return "
      <div class='ltkwidget-widget' data-rows='$rows' data-cols='$cols' data-show-frame='$show_frame' data-user-id='$user_id' data-padding='$padding'>
        <script>{$this->load_javascript('//widgets.rewardstyle.com/js/ltkwidget.js', 'ltkwidget-script')}'object'==typeof window.__ltkwidget&&'complete'===document.readyState&&__ltkwidget.init();</script>
        {$this->adblock_html}
      </div>
    ";
  }

  public function ltk_widget_version_two($atts) {
    extract(shortcode_atts(array(
      'app_id'       => '0',
      'user_id'      => '0',
      'rows'         => '1',
      'cols'         => '6',
      'show_frame'   => 'true',
      'padding'      => '0',
      'display_name' => '',
      'profileid'    => ''
    ), $atts));

    return "
      <div id='$app_id' data-appid='$app_id' class='ltkwidget-version-two'>
        <script>
          var rsLTKLoadApp='0', rsLTKPassedAppID=\'$app_id\';
          {$this->load_javascript('//widgets-static.rewardstyle.com/widgets2_0/client/pub/ltkwidget/ltkwidget.js', 'ltkwidget-version-two-script')}
        </script>
        <div widget-dashboard-settings='' data-appid='$app_id' data-userid='$user_id' data-rows='$rows' data-cols='$cols' data-showframe='$show_frame' data-padding='$padding' data-displayname='$display_name' data-profileid='$profileid'><div class='rs-ltkwidget-container'><div ui-view=''></div></div></div>
      </div>
    ";
  }

  public function lookbook_show_widget($atts) {
    extract(shortcode_atts(array(
      'id'       => '0',
      'adblock'  => $this->adblock_message,
      'enableJs' => $this->enablejs_message
    ), $atts));

    $this->adblock_message = $adblock;
    $this->enablejs_message = $enableJs;

    return "
      <div class='lookbook-widget' data-widget-id='$id'>
        <script>
        {$this->load_javascript('//widgets.rewardstyle.com/js/lookbook.js', 'lookbook-script')}'object'==typeof window.__lookbook&&'complete'===d.readyState&&window.__lookbook.init();
        </script>
        {$this->adblock_html}
      </div>
    ";
  }

  public function shopthepost_show_widget($atts) {
    extract(shortcode_atts(array(
      'id'       => '0',
      'adblock'  => $this->$adblock_message,
      'enableJs' => $this->$enablejs_message
    ), $atts));

    $this->adblock_message = $adblock;
    $this->enablejs_message = $enableJs;

    return "
      <div class='shopthepost-widget' data-widget-id='$id'>
        <script>
          {$this->load_javascript('//widgets.rewardstyle.com/js/shopthepost.js', 'shopthepost-script')}'object'==typeof window.__stp&&'complete'===d.readyState&&window.__stp.init();
        </script>
        {$this->adblock_html}
      </div>
    ";
  }

  function boutique_show_widget($atts) {
    extract(shortcode_atts(array(
      'id'       => '0',
      'adblock'  => $this->$adblock_message,
      'enableJs' => $this->$enablejs_message
    ), $atts));

    $this->adblock_message = $adblock;
    $this->enablejs_message = $enableJs;

      return "
      <div class='boutique-widget' data-widget-id='$id'>
        <script>
          {$this->load_javascript('//widgets.rewardstyle.com/js/boutique.js', 'boutique-script')}'object'==typeof window.__boutique&&'complete'===d.readyState&&window.__boutique.init();
        </script>
        {$this->adblock_html}
      </div>
    ";
  }
}