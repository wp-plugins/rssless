<?php
/*
Plugin Name: RSSless
Plugin URI: http://simply-basic.com/rssless-plugin
Description: Allows you to remove specific content of your posts from RSS feeds and replace it with any customizable message by using <code>[rssless][/rssless]</code> short codes. Useful for removing embedded videos or images which don't display properly in RSS readers.
Author: John Kolbert
Version: 1.0
Author URI: http://simply-basic.com/

Copyright 2008 by John Kolbert (aka Simply-Basic.com)

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the “Software”), to deal in
the Software without restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

function rssless_option_menu() {  // install the options menu
	if (function_exists('current_user_can')) {
		if (!current_user_can('manage_options')) return;
	} else {
		global $user_level;
		get_currentuserinfo();
		if ($user_level < 8) return;
	}
	if (function_exists('add_options_page')) {
		add_options_page(__('RSSless'), __('RSSless'), 1, __FILE__, 'rssless_options_page');
	}
} 

// Install the options page
add_action('admin_menu', 'rssless_option_menu');

function rssless_options_page(){

	global $wpdb;

	if (isset($_POST['update_options'])) {
	$options['rssless_text'] = trim($_POST['rssless_text'],'{}');

    update_option('rssless_options', $options);
		// Show a message to say we've done something
		echo '<div class="updated"><p>' . __('Options saved') . '</p></div>';
	} else {
		
		$options = get_option('rssless_options');
	}
	 
	?>
		<div class="wrap">
		<h2><?php echo __('RSSless Options Page'); ?></h2>
    <p>Created by <a href="http://simply-basic.com/">John Kolbert</a><br />
    Detailed Readme: <a href="http://simply-basic.com/rssless-plugin">http://simply-basic.com/rssless-plugin</a></p>
	<p>RSSless allows you to remove partial content of your posts from RSS feeds by using shortcodes. This is useful for removing embedded videos, images, or other content which doesn't display properly or you don't want shown in RSS readers.</p>
	

			<form method="post" action="">
      <h3>General Options</h3>
      <table class="form-table">
      		<tr><th scope="row">Default Replacement Message: <br /><textarea name="rssless_text" id="rssless_text" rows="5" cols="60"><?php echo stripslashes($options['rssless_text']); ?></textarea></th><td>This is the default message that will be displayed in RSS readers in place of your chosen post content. You may include any arbitrary HTML.</td>
			</tr>
			</table>
		
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Update') ?>"  style="font-weight:bold;" /> </div>
		<p>If you find this free plugin useful, help support future development by donating any amount securely through PayPal.
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_donations">
<input type="hidden" name="business" value="admin@simply-basic.com">
<input type="hidden" name="item_name" value="Support Free Plugins">
<input type="hidden" name="no_shipping" value="0">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="tax" value="0">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="bn" value="PP-DonationsBF">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>   		
		
		<h2>Usage</h2>
		<p>You can use the RSSless short code in two ways. First, you can replace your hidden content with your default replacement message
		by surrounding your content to be hidden in the following manner:</p>
		
		<p><code>[rssless] My content to hide [/rssless]</code></p>
		
		<p>Alternatively, you can supply a custom replacement message for any short code by surrounding your content in the following manner:</p>
		
		<p><code>[rssless title="The text to be shown"] My content to hide [/rssless]</code></p>
		</form> 
    	
	</div>
	<?php	
}

// [rssless text="replacement text"]
function rssless_tags($atts, $content = null) {
	$options = get_option('rssless_options');

	extract(shortcode_atts(array(
		'text' => stripslashes($options['rssless_text']),  //sets the default value if no "text" attribute is set
	), $atts));

	if (is_feed()){ //return the replacement if were in an RSS feed
		return $text;
	}
	else{
		return do_shortcode($content); //otherwise return the content, but make sure to do any short codes that are inside it
	}
}

add_shortcode('rssless', 'rssless_tags'); //install the shortcode
?>