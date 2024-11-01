<?php
/**
 * @package SiteObservers
 * @version 1.0
 */
/*
Plugin Name: SiteObservers for Wordpress
Plugin URI: http://www.siteobservers.com
Description: SiteObservers plugin is the <strong>FIRST</strong> cloud-based monitoring solution that is capable of monitoring the availability, performance, and functionalities of any WordPress plugins. It enables you to monitor the end-users' experience of your web system. To get started: 1) Click the "Activate" link to the left of this description, 2) Provide your SiteObservers’ registration credentials on the plugin <a href="./admin.php?page=siteobservers-settings">Settings</a> page.
Author: SiteObservers
Version: 1.0
Author URI: http://www.siteobservers.com
*/

add_action( 'admin_menu', 'register_siteobservers_menu' );

function check_pswrd( $pawrd ) {
  if( $pawrd == str_repeat("*", strlen($pawrd)) )
    return get_option('siteobservers_password');
  return $pawrd;
}

function register_mysettings() {
  register_setting( 'siteobservers-group', 'siteobservers_login' );
  register_setting( 'siteobservers-group', 'siteobservers_password', 'check_pswrd' );
}

function register_siteobservers_menu(){
  add_menu_page( "SiteObservers", "SiteObservers", 8, "siteobservers_main_menu", "show_siteobservers_menu" );
  add_submenu_page( "siteobservers_main_menu", 'SiteObservers', 'Control Panel', 8, "siteobservers_main_menu", 'show_siteobservers_menu' );
  add_submenu_page( "siteobservers_main_menu", 'SiteObservers', 'Settings', 8, "siteobservers-settings", 'show_siteobservers_settings' );
  add_submenu_page( "siteobservers_main_menu", 'SiteObservers', 'Get Started', 8, "siteobservers-get-started", 'siteobservers_get_started' );
  add_action( 'admin_init', 'register_mysettings' );
}

function show_siteobservers_menu() {
  $url = "https://app.siteobservers.com/Login.aspx";
  $username = get_option( 'siteobservers_login' );
  $password = get_option( 'siteobservers_password' );
  $postinfo = "login=".$username."&password=".$password."&auto_login_key=1";
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_NOBODY, false);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
  $result = curl_exec($ch);
  curl_close($ch);

  $sbt_auto_login_key = "";
  $lines = preg_split('/\r\n|\n|\r/', $result);
  foreach( $lines as $value ) {
    if( stripos( $value, "Set-SbtAutoLoginKey1: ") !== false ){
      $sbt_auto_login_key = str_replace( "Set-SbtAutoLoginKey1: ", "", $value );
    }
  }
  echo "<iframe style='display: block; width: 98%; height: 700px;' name='sbt_word_press_plugin' src='https://app.siteobservers.com/Login.aspx?ReturnUrl=Monitor%2fDashboard.aspx&auto_login_key=$sbt_auto_login_key' scrolling='no'></iframe>";
}

function show_siteobservers_settings(){
?>
<span style="display: block; width: 100%; overflow: hidden; margin: 10px 0px 0px 15px;">
<span style="display: block; clear: both; width: 172px; height: 30px; overflow: hidden; background: url(../wp-content/plugins/siteobservers-for-wordpress/siteobservers-logo.png)"></span>
<span style="display: block; width: 100%; font-size: 22px; font-weight: 800; margin: 25px 0px 0px 0px;">General Settings</span>
<span style="display: block; width: 100%; overflow: hidden; margin: 15px 0px 0px 0px;">
<span style="display: block; width: 100%; margin: 10px 0px 0px 0px; font-size: 16px; font-weight: bold;">Users with existing SiteObservers account</span>
  <span style="display: block; float: left; margin: 15px 0px 0px 0px; color: #555; font-size: 14px;">Please enter your SiteObservers login information.</span>
</span>

<form method="post" action="options.php">
<?php settings_fields( 'siteobservers-group' ); ?>

  <span style="display: block; width: 100%; overflow: hidden; margin: 10px 0px 0px 0px;">
    <span style="display: block; width: 100%; overflow: hidden; margin: 10px 0px 0px 0px;">
      <span style="display: block; float: left; width: 200px; text-align: left; font-size: 14px; ">SiteObservers Login</span>
      <input style="display: block; float: left; margin: 0px 0px 0px 10px; width: 300px;" type="text" name="siteobservers_login" value="<?php echo get_option('siteobservers_login'); ?>" />
    </span>
    <span style="display: block; width: 100%; overflow: hidden; margin: 10px 0px 0px 0px;">
      <span style="display: block; float: left; width: 200px; text-align: left; font-size: 14px; ">SiteObservers Password</span>
      <input style="display: block; float: left; margin: 0px 0px 0px 10px; width: 300px;" type="password" name="siteobservers_password" value="<?php $_p=get_option('siteobservers_password'); if($_p) echo str_repeat("*", strlen($_p)); else echo ""; ?>" />
    </span>
  </span>
  <span style="display: block; width: 100%; overflow: hidden; margin: 15px 0px 0px 0px;">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </span>
</form>
<span style="display: block; float: left; width: 100%; margin: 20px 0px 30px 0px;">
    <a style="text-decoration: none; font-size: 14px; " href="admin.php?page=siteobservers_main_menu">Navigate to SiteObservers Control Panel</a>
</span>

<span style="display: block; width: 100%; margin: 35px 0px 0px 0px; font-size: 16px; font-weight: bold;">First time SiteObservers users</span>
<span style="display: block; float: left; margin: 15px 0px 0px 0px; color: #555; font-size: 14px;">If you do not yet have an SiteObserver account <a style="color: #0078ca; text-decoration: none; target="_blank" href="https://app.siteobservers.com/signup.aspx">click here</a>, to get one <strong>Free Account</strong>.</span>


</span>
<?php
}

function siteobservers_get_started() {
?>
<span style="display: block; width: 100%; overflow: hidden; margin: 10px 0px 0px 15px; ">
<span style="display: block; clear: both; width: 172px; height: 30px; overflow: hidden; background: url(../wp-content/plugins/siteobservers-for-wordpress/siteobservers-logo.png)"></span>
<span style="display: block; width: 85%; color: #555; font-size: 14px;">
  <span style="display: block; width: 100%; margin: 25px 0px 0px 0px; font-size: 22px; font-weight: bold; color: #000;">Get Started</span>
  <span style="display: block; width: 100%; margin: 25px 0px 0px 0px; font-size: 16px; font-weight: bold;">What is SiteObservers for Wordpress plugin?</span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px;">SiteObservers for Wordpress is a cloud based enterprise-class IT service to track the availability and performance of <span style="font-weight: bold; color: #000;">servers, applications, and Wordpress plugins</span> on one unified platform.</span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px;">This is the <span style="font-weight: bold; color: #000;">first all-in-one</span> monitoring solutions for server, applications and Wordpress plugins to help you safeguard and enhance your online business.</span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px;">It offers a <span style="font-weight: bold; color: #000;">free account</span> to start with that never expires. The paid plans are the most competitive and affordable in the marketplace.</span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px;">The SiteObservers monitoring service is also able to monitor your servers, applications and Wordpress plugins through its <span style="font-weight: bold; color: #000;">own lightweight, simple and efficient Wordpress plugin</span> to be installed on your Wordpress website. We offer multiple service levels – free services, low price services, and fully managed services – on one unified platform that are suitable for all kinds of customers like SMBs and large corporations.</span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px; font-size: 16px; font-weight: bold;">How to start monitoring my Wordpress?</span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px;">It is very simple and easy to start monitoring the performance of your Wordpress servers’ health with the SiteObservers’ unified monitoring service through an efficient Wordpress plugin in 3 easy steps.</span>
  <span style="display: block; width: 100%;">
    <span style="display: block; padding: 10px 0px 0px 20px; ">
      <span style="display: block; width: 100%;">1. Sign up for a <span style="font-weight: bold; color: #000;">free</span> account on the SiteObservers monitoring service platform.</span>
      <span style="display: block; width: 100%;">2. Download <span style="font-weight: bold; color: #000;">SiteObservers Wordpress plugin</span> from WordPress plugin directory and install it on your WordPress website.</span>
      <span style="display: block; width: 100%;">3. Provide your SiteObservers’ registration credentials on the <span style="font-weight: bold; color: #000;">Settings page</span>, and click on the <span style="font-weight: bold; color: #000;">Control Panel</span> option under SiteObservers plugin section.</span>
    </span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px;"><span style="font-weight: bold; color: #000;">DONE!</span> You are ready to start monitoring your Wordpress blog or website.</span>
  <span style="display: block; width: 100%; margin: 10px 0px 0px 0px;">We will provide <span style="font-weight: bold; color: #000;">interactive tour</span> how to create monitors.</span>
  <span style="display: block; width: 100%; margin: 20px 0px 0px 0px;">
    <a style="text-decoration: none; color: #0078ca; font-size: 14px; " href="admin.php?page=siteobservers-settings">Navigate to SiteObservers Settings Page</a>
  </span>
</span>
<?php
}

?>
