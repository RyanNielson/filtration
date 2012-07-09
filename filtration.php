<?php

/*
Plugin Name: Filtration
Plugin URI: https://github.com/RyanNielson/filtration
Description: Filters out user-defined words from your site.
Version: 1.0
Author: RyanNielson
Author URI: https://github.com/RyanNielson
*/

/* Add Copyright and License */


add_action('admin_menu', 'filt_add_options_page');
function filt_add_options_page() {
	add_options_page('Filtration Options', 'Filtration', 'manage_options', __FILE__, 'filt_render_options_page');
}

add_action('admin_init', 'filt_init');
function filt_init(){
	register_setting('filt_plugin_options', 'filt_options');
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
						<textarea name="filt_options[filter_nonstrict_keywords]" rows="7" cols="50" type='textarea'><?php echo $options['filter_nonstrict_keywords']; ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">Strict Words
						<br/>
						<span class="description">Words that will be censored, no matter where they appear in the text. Must be provided in a comma seperated format.</span>
					</th>
					<td>
						<textarea name="filt_options[filter_strict_keywords]" rows="7" cols="50" type='textarea'><?php echo $options['filter_strict_keywords']; ?></textarea><br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Filtered content</th>
					<td>
						<input if="filter-post-content" name="filt_options[filter_post_content]" type="checkbox" value="1" <?php if (isset($options['filter_post_content'])) { checked('1', $options['filter_post_content']); } ?> /> 
						<label for="filter-post-content">Post/Page Content</label>
						<br />

						<input name="filt_options[filter_post_titles]" type="checkbox" value="1" <?php if (isset($options['filter_post_titles'])) { checked('1', $options['filter_post_titles']); } ?> /> 
						<label for="filter-post-content">Post/Page Titles</label>
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