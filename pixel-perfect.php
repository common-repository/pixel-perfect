<?php
/*
Plugin Name: Pixel Perfect
Plugin URI: http://www.musenuovo.co.uk
Description: Places image of your choice to overlay the site you are developing. You can hide it and bring it back to foreground at opacity you specify. Develope Wordpress sites faster and Pixel Perfect.
Version: 1.3
Author: Michal Balazi
Author URI: http://www.musenuovo.co.uk
License: GPL2
*/

/*  Copyright 2012  Michal Balazi  (email : michal_balazi@yahoo.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}


// Check if admin menu
if ( is_admin() || is_login_page() ) {



// Add custom styles
function pxp_admin_register_styles() {
add_action( 'pxp_styles', 'enqueue_my_styles' );
wp_enqueue_style( 'pxp_styles', '/wp-content/plugins/pixel-perfect/css/pixel-perfect-admin.css');
}
add_action('admin_head', 'pxp_admin_register_styles');



// display the option page
function pxp_option_page() {
?>
<div class="wrap" id="pxp_options">
<?php screen_icon(); ?>
<h2>Pixel Perfect Options</h2>
<div id="main">
<div class="description">
<p>Welcome to Pixel Perfect plugin options menu. Upload image of your design. Plugin will than overlay the page you specify. You can set default opacity of the overlay, set visible portion of the overlay.</p>
</div><!--.description-->

<div class="options">
<form action="options.php" method="post" enctype="multipart/form-data" id="pxp_options_form">
<?php settings_fields('pxp_options');  ?>
<ul>

<li>
<label for="pxp_active">Active</label>
<input type="checkbox" id="pxp_active" name="pxp_active" value="Y" <?php if(get_option('pxp_active')=='Y') {echo'checked="checked"';} ?> /><span>Check to activate</span>
</li>

<li>
<label for="pxp_restrict">Show only to admin</label>
<input type="checkbox" id="pxp_restrict" name="pxp_restrict" value="Y" <?php if(get_option('pxp_restrict')=='Y') {echo'checked="checked"';} ?> /><span>Check to activate</span>
</li>


<li>
<label for="pxp_height">Overlay Height</label>
<input type="text" id="pxp_height" name="pxp_height" value="<?php echo get_option('pxp_height'); ?>" />
<span>px</span>
</li>

<li>
<label for="pxp_opacity">Opacity</label>
<input type="text" id="pxp_opacity" name="pxp_opacity" value="<?php echo get_option('pxp_opacity'); ?>" />
<span> % opacity (range 1 - 100)</span>
</li>

<li>
<label for="pxp_page">Display on Page</label>
<select name="pxp_page" id="pxp_page"> 
 <?php 
  $pages = get_pages(); 
  foreach ( $pages as $page ) {
  if($page->ID == get_option('pxp_page')) {  	$option = '<option value="' . $page->ID . '" selected="selected">';} 
  else {  	$option = '<option value="' . $page->ID . '">'; }
	$option .= $page->post_title;
	$option .= '</option>';
	echo $option;
  }
 ?>
<option disabled role="separator">------------------</option> 
<option value="blog" <?php if(get_option('pxp_page')=="blog") {echo 'selected="selected"';} ?>>Blog</option>
<option disabled role="separator">------------------</option> 
<option value="single" <?php if(get_option('pxp_page')=="single") {echo 'selected="selected"';} ?>>Single Post</option>
</select>
<span>Select which page to display the overlay on.</span>
</li>

<li>
<?php if(get_option('pxp_image')){ 
$image = get_option('pxp_image'); echo '<img src="' .$image['pxp_image'] . '" alt="" width="200" />'; 
?>

<input type="submit" name="pxp_delete_img" value="Delete Image" class="button-secondary" id="pxp_delete" />



<?php } else { ?>

<label for="pxp_image">Overlay Image</label>
<input type="file" id="pxp_image" name="pxp_image" />

<?php } ?>
</li>

<li>
<input type="submit" name="submit" value="Save Options" class="button-primary" />
</li>
</ul>
</form>

</div><!--.options-->
</div><!--#main -->

<div id="sidebar">
</div>
</div><!--#wrap-->

<?php
}





function validate_image($plugin_options) { 


$keys = array_keys($_FILES); 
$i = 0; 

foreach ( $_FILES as $image ) {
// if a files was upload   

if ($image['size']) {   

// if it is an image     
if ( preg_match('/(jpg|jpeg|png|gif)$/', $image['type']) ) {       

$override = array('test_form' => false);       
// save the file, and store an array, containing its location in $file       
$file = wp_handle_upload( $image, $override );       
$plugin_options[$keys[$i]] = $file['url'];     

} else {

// Die and let the user know that they made a mistake.       
wp_die('No image was uploaded.');     

}    

// Else, the user didn't upload a file.   
// Retain the image that's already on file.   

} else {     

$options = get_option('pxp_image');
$plugin_options[$keys[$i]] = $options[$keys[$i]];

}   

$i++; 
} return $plugin_options;
   

}







// Validate numbers
function validate_number($input){

// Create our array for storing the validated options  
$output = array();  

$output = strip_tags( stripslashes( $input ) );  
if (!ctype_digit( $output ) )  {$output='';}

// Return the array processing any additional functions filtered by this action  
return apply_filters( 'sandbox_theme_validate_input_examples', $output, $input );  
  
} // end validate_input



// Validate input
function validate_input($input){

// Create our array for storing the validated options  
$output = array();  
$output = strip_tags( stripslashes( $input ) );  

return apply_filters( 'validate_input', $output, $input ); 
} // end validate_input



// register fields
function pxp_init(){
if(get_option('pxp_image')) {$image = get_option('pxp_image');} else {$image['pxp_image']='';}

register_setting('pxp_options', 'pxp_active', 'validate_input');
register_setting('pxp_options', 'pxp_restrict', 'validate_input');
register_setting('pxp_options', 'pxp_height', 'validate_number');
register_setting('pxp_options', 'pxp_opacity', 'validate_number');
register_setting('pxp_options', 'pxp_page', 'validate_input');
if($image['pxp_image']=='' || array_key_exists('pxp_delete_img', $_POST)) {register_setting('pxp_options', 'pxp_image', 'validate_image');}
}
add_action('admin_init', 'pxp_init');


// create menu in the admin
function pxp_plugin_menu(){
add_options_page('Pixel Perfect Settings', 'Pixel Perfect', 'manage_options', 'pixel-perfect-plugin', 'pxp_option_page');
}
add_action('admin_menu', 'pxp_plugin_menu');

} else {
// We are in the frontend


// Check what page we are on (Page, Single or Blog)
function pxp_image_overlay(){

// Check if plugin active
if( get_option('pxp_active')=='Y'){
if ( get_option('pxp_restrict')=='' || get_option('pxp_restrict')=='Y' && current_user_can('manage_options') ) {


// Overlay function
function pxp_overlay() {

if(get_option('pxp_image')){
$topspacing = '0';
if(current_user_can('manage_options')) {$topspacing = '1';}
$image = get_option('pxp_image');
echo '<img src="' .$image['pxp_image']. '" id="pxp-image" height="'. get_option('pxp_height') .'" opacity="'. get_option('pxp_opacity') .'" topspacing="'. $topspacing .'" />';

add_action( 'pxp_styles', 'enqueue_my_styles' );
add_action( 'pxp_jquery_ui_css', 'enqueue_my_styles' );
add_action( 'pxp_jquery_ui', 'enqueue_my_scripts' );
add_action( 'pxp_java_script', 'enqueue_my_scripts' );
wp_enqueue_style( 'pxp_jquery_ui_css', '/wp-content/plugins/pixel-perfect/css/jquery-ui.css');
wp_enqueue_style( 'pxp_styles', '/wp-content/plugins/pixel-perfect/css/pixel-perfect.css');
wp_enqueue_script( 'pxp_jquery_ui', 'http://code.jquery.com/ui/1.10.2/jquery-ui.js', array( 'jquery' ));
wp_enqueue_script( 'pxp_java_script', '/wp-content/plugins/pixel-perfect/js/pixel-perfect.js', array( 'jquery' ));
}

} // end of pxp_overlay()




// if blog page
if(get_option('pxp_page')=='blog' && is_home()){

pxp_overlay();


// if single post
} else if( get_option('pxp_page')=='single' && is_single() ){

pxp_overlay();

// if standard page
} else if( is_page(get_option('pxp_page')) ) {

pxp_overlay();

}


} // end of check if pxp restricted to loged in users
} // end of check if pxp active



} // end pxp_image_overlay()

add_action( 'get_header' , 'pxp_image_overlay');


} // end of check for frontend







// register default values for options when activating the plugin
function pxp_activate() {
update_option( 'pxp_active', 'Y' );
update_option( 'pxp_restrict', 'Y' );
update_option( 'pxp_height', '1200' );
update_option( 'pxp_opacity', '30' );
update_option('pxp_image', '');
}
register_activation_hook(__FILE__, 'pxp_activate' );



// delete options when uninstalling the plugin
function pxp_uninstall() {
delete_option('pxp_active');
delete_option('pxp_restrict');
delete_option('pxp_height');
delete_option('pxp_opacity');
delete_option('pxp_page');
delete_option('pxp_image');
}
register_uninstall_hook(__FILE__, 'pxp_uninstall');
?>