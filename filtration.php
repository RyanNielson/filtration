<?php

/*
Plugin Name: Filtration
Plugin URI: https://github.com/RyanNielson/filtration
Description: Filters out user-defined words from your site.
Version: 1.0
Author: RyanNielson
Author URI: https://github.com/RyanNielson
*/

add_action('admin_menu', 'filt_add_options_page');
add_action('plugins_loaded', 'filt_add_filters');
add_action('admin_init', 'filt_init');
add_action('plugins_loaded', 'filt_filter');
register_uninstall_hook(__FILE__, 'filt_delete_options');

function filt_add_options_page() {
	add_options_page('Filtration Options', 'Filtration', 'manage_options', __FILE__, 'filt_render_options_page');
}

function filt_init() {
	register_setting('filt_plugin_options', 'filt_options');
}

function filt_filter() {
	$options = get_option('filt_options');
}

function filt_render_options_page() {
	?>

	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Filtration Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields('filt_plugin_options'); ?>
			<?php $options = get_option('filt_options'); ?>
			<table class="form-table">
				<tr>
					<th scope="row">Non-strict Words
						<br/>
						<span class="description">Words that will be censored if they're on their own. They must be provided in a comma seperated format.</span>
					</th>
					<td>
						<textarea name="filt_options[filter_nonstrict_keywords]" rows="10" cols="60"><?php echo $options['filter_nonstrict_keywords']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">Strict Words
						<br/>
						<span class="description">Words that will be censored, no matter where they appear in the text. Must be provided in a comma seperated format.</span>
					</th>
					<td>
						<textarea name="filt_options[filter_strict_keywords]" rows="10" cols="60"><?php echo $options['filter_strict_keywords']; ?></textarea><br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Filtered content</th>
					<td>
						<input id="filter-post-content" name="filt_options[filter_post_content]" type="checkbox" value="1" <?php if (isset($options['filter_post_content'])) { checked('1', $options['filter_post_content']); } ?> /> 
						<label for="filter-post-content">Post/Page Content</label>
						<br />

						<input id="filter-post-title" name="filt_options[filter_post_title]" type="checkbox" value="1" <?php if (isset($options['filter_post_title'])) { checked('1', $options['filter_post_title']); } ?> /> 
						<label for="filter-post-title">Post/Page Titles</label>
						<br />

						<input id="filter-comments" name="filt_options[filter_comments]" type="checkbox" value="1" <?php if (isset($options['filter_comments'])) { checked('1', $options['filter_comments']); } ?> /> 
						<label for="filter-comments">Comments</label>
						<br /><br />
					</td>
				</tr>
				<tr>
					<th scope="row">Filter Character
						<br/>
						<span class="description">The character that will replace the letters in the filtered word.</span>
					</th>
					<td>
						<input name="filt_options[filter_character]" type="text" value="<?php if (isset($options['filter_character'])) { echo $options['filter_character']; } ?>" /> 
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>

	<?php	
}

function filt_add_filters() {
    $options = get_option('filt_options');
    
	if (isset($options['filter_post_content']) && $options['filter_post_content'] == '1')
		add_filter('the_content', 'filt_filter_text');
	if (isset($options['filter_post_title']) && $options['filter_post_title'] == '1')
		add_filter('the_title', 'filt_filter_text');
	if (isset($options['filter_comments']) && $options['filter_comments'] == '1')
		add_filter('comment_text', 'filt_filter_text');
}

function filt_filter_text($text) {
	$options = get_option('filt_options');

	$filter_character = $options['filter_character'];
	$strict_replacement_keywords = explode(',', $options['filter_strict_keywords']);
	$nonstrict_replacement_keywords = explode(',', $options['filter_nonstrict_keywords']);

    array_walk($strict_replacement_keywords, 'filt_trim_keywords');
    array_walk($nonstrict_replacement_keywords, 'filt_trim_keywords');
    
    // Remove duplicate keywords.
    $strict_replacement_keywords = array_unique($strict_replacement_keywords);
    $nonstrict_replacement_keywords = array_unique($nonstrict_replacement_keywords);

    // Replace strict keywords.
    foreach($strict_replacement_keywords as $keyword) {
    	$replacement = str_repeat($filter_character, strlen($keyword));
    	$text = str_ireplace($keyword, $replacement, $text);
	}

	// Replace non-strict keywords
	foreach($nonstrict_replacement_keywords as $keyword) {
		$replacement = str_repeat($filter_character, strlen($keyword));
    	$text = str_ireplace_nonstrict($keyword, $replacement, $text);
	}
   
    return $text;
}

function filt_trim_keywords(&$item) {
    $item = trim($item);
}

function filt_delete_options() {
	delete_option('filt_options');
}

// Replace the haystack with the replacement if the needle is on its own.
function str_ireplace_nonstrict($needle, $replacement, $haystack) {
    return preg_replace("/\b$needle\b/i", $replacement, $haystack);
}