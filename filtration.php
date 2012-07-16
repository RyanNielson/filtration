<?php

/*
Plugin Name: Filtration
Plugin URI: https://github.com/RyanNielson/filtration
Description: Filters out user-defined words from your site.
Version: 1.1
Text Domain: filtration
Author: Ryan Nielson
Author URI: https://github.com/RyanNielson
*/

/*  Copyright 2012  Ryan Nielson

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

class WPFiltration
{
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'plugins_loaded', array( $this, 'add_filters' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		register_uninstall_hook( __FILE__, array( $this, 'delete_options' ) );
	}

	public function add_options_page() {
		add_options_page(
			__('Filtration Options', 'filtration'), 
			__('Filtration', 'filtration'),
			'manage_options',
			'filtration-options',
			array( $this, 'render_options_page' )
		);
	}

	public function init() {
		register_setting(
			'filt_plugin_options',
			'filt_options',
			array( $this, 'settings_cleaner' )
		);
	}

	function settings_cleaner( $in ) {
		$out = array();

		$kws = array( 'filter_nonstrict_keywords', 'filter_strict_keywords' );
		foreach( $kws as $kw ) {
			if( isset($in[$kw]) ) {
				$kw_arr = explode(',', $in[$kw] );
				array_walk( $kw_arr, 'esc_attr' );
				array_walk( $kw_arr, 'trim' );
				$out[$kw] = implode( ',', $kw_arr );
			} else {
				$out[$kw] = '';
			}
		}

		$checks = array(
			'filter_post_title',
			'filter_post_content',
			'filter_comments'
		);
		foreach( $checks as $c ) {
			$out[$c] = isset( $in[$c] ) && $in[$c] ? 1 : 0;
		}

		// make sure the filter character is one character
		if( isset($in['filter_character']) ) {
			$out['filter_character'] = esc_attr( substr( $in['filter_character'], 0, 1 ) );
		}

		return $out;
	}

	function render_options_page() {
		$options = get_option( 'filt_options' );
		$non_strict = isset( $options['filter_nonstrict_keywords'] ) ? $options['filter_nonstrict_keywords'] : '';
		$strict = isset( $options['filter_strict_keywords'] ) ? $options['filter_strict_keywords'] : '';
		$filter_char = isset( $options['filter_character'] ) ? $options['filter_character'] : '';

		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php esc_html_e( 'Filtration Options', 'filtration' ); ?></h2>

			<form method="post" action="options.php">
				<?php settings_fields( 'filt_plugin_options' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Non-strict Words', 'filtration'); ?>
							<br/>
							<span class="description">
								<?php esc_html_e( "Words that will be censored if they're on their own. They must be provided in a comma seperated format.", 'filtration' ); ?>
							</span>
						</th>
						<td>
							<textarea name="filt_options[filter_nonstrict_keywords]" rows="10" cols="60"><?php echo esc_textarea( $non_strict ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Strict Words', 'filtration' ); ?>
							<br/>
							<span class="description">
								<?php esc_html_e( 'Words that will be censored, no matter where they appear in the text. Must be provided in a comma seperated format.', 'filtration' ); ?>
							</span>
						</th>
						<td>
							<textarea name="filt_options[filter_strict_keywords]" rows="10" cols="60"><?php echo esc_textarea( $strict ); ?></textarea><br />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Filtered content', 'filtration'); ?></th>
						<td>
							<input id="filter-post-content" name="filt_options[filter_post_content]" type="checkbox" value="1" <?php if ( isset( $options['filter_post_content'] ) ) { checked( '1', $options['filter_post_content'] ); } ?> /> 
							<label for="filter-post-content"><?php esc_html_e( 'Post/Page Content', 'filtration' ); ?></label>
							<br />

							<input id="filter-post-title" name="filt_options[filter_post_title]" type="checkbox" value="1" <?php if ( isset( $options['filter_post_title'] ) ) { checked( '1', $options['filter_post_title'] ); } ?> /> 
							<label for="filter-post-title"><?php esc_html_e( 'Post/Page Titles', 'filtration' ); ?></label>
							<br />

							<input id="filter-comments" name="filt_options[filter_comments]" type="checkbox" value="1" <?php if ( isset( $options['filter_comments'] ) ) { checked( '1', $options['filter_comments'] ); } ?> /> 
							<label for="filter-comments"><?php esc_html_e( 'Comments', 'filtration' ); ?></label>
							<br /><br />
						</td>
					</tr>
					<tr>
					<th scope="row">
							<?php esc_html_e( 'Filter Character', 'filtration' ); ?>
							<br/>
							<span class="description">
								<?php esc_html_e( 'The character that will replace the letters in the filtered word.', 'filtration' ); ?>
							</span>
						</th>
						<td>
							<input name="filt_options[filter_character]" type="text" value="<?php echo esc_attr( $filter_char ); ?>" />
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'filtration') ?>" />
				</p>
			</form>
		</div>

		<?php   
	}

	function add_filters() {
		$options = get_option( 'filt_options' );
		
		if ( isset($options['filter_post_content']) && $options['filter_post_content'] == '1' )
			add_filter( 'the_content', array( $this, 'filter_text' ) );
		if ( isset($options['filter_post_title']) && $options['filter_post_title'] == '1' )
			add_filter( 'the_title', array( $this, 'filter_text' ) );
		if ( isset($options['filter_comments']) && $options['filter_comments'] == '1' )
			add_filter( 'comment_text', array( $this, 'filter_text' ) );
	}

	function filter_text( $text ) {
		$options = get_option('filt_options');

		$filter_character = $options['filter_character'];
		$strict_replacement_keywords = explode( ',', $options['filter_strict_keywords'] );
		$nonstrict_replacement_keywords = explode( ',', $options['filter_nonstrict_keywords'] );

		array_walk( $strict_replacement_keywords, array( $this, 'trim_keywords' ) );
		array_walk( $nonstrict_replacement_keywords, array( $this, 'trim_keywords' ) );
		
		// Remove duplicate keywords.
		$strict_replacement_keywords = array_unique( $strict_replacement_keywords );
		$nonstrict_replacement_keywords = array_unique( $nonstrict_replacement_keywords );

		// Replace strict keywords.
		foreach( $strict_replacement_keywords as $keyword ) {
			$replacement = str_repeat( $filter_character, strlen( $keyword ) );
			$text = str_ireplace( $keyword, $replacement, $text );
		}

		// Replace non-strict keywords
		foreach( $nonstrict_replacement_keywords as $keyword ) {
			$replacement = str_repeat( $filter_character, strlen( $keyword ) );
			$text = $this->str_ireplace_nonstrict( $keyword, $replacement, $text );
		}
	   
		return $text;
	}

	function trim_keywords( &$item ) {
		$item = trim( $item );
	}

	function delete_options() {
		delete_option( 'filt_options' );
	}

	// Replace the haystack with the replacement if the needle is on its own.
	function str_ireplace_nonstrict( $needle, $replacement, $haystack ) {
		return preg_replace(
			sprintf( '/\b%s\b/i', preg_quote( $needle, '/' ) ),
			$replacement,
			$haystack
		);
	}
}

$filtration_plugin = new WPFiltration();

?>
