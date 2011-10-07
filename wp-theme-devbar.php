<?php
/*
Plugin Name: WP Theme Devbar
Plugin URI: http://voceconnect.com
Description: Adds info bar to help give insight to current template, rewriterule, and other valuable information while developing.
Author: prettyboymp
Version: 0.1
Author URI: http://voceconnect.com
*/

class WP_Theme_Devbar {
	private $loaded_template;
	public function init() {
		if(  current_user_can( 'manage_options')) {
			$this->loaded_template = '';
			add_action('template_redirect', array($this, '_action_template_redirect'));
			add_action('wp_footer', array($this, '_action_wp_footer'), 1000);
			add_filter('template_include', array($this, '_filter_template_include'));
		}
	}
	
	public function _filter_template_include($template) {
		$this->loaded_template = $template;
		return $template;
	}
	
	public function _action_template_redirect() {
		wp_enqueue_style('theme-devbar', plugins_url('/style.css', __FILE__));
	}
	
	public function _action_wp_footer() {
		global $wp, $wp_rewrite, $wp_query;
		?>
		<div id='dev-output'>
			<table>
				<tr>
					<td>Loaded Template</td>
					<td><?php echo esc_html($this->loaded_template); ?></td>
				</tr>
				<tr>
					<td>Matched Rewrite Rule</td>
					<td>'<?php echo esc_html($wp->matched_rule); ?>' => '<?php echo esc_html($wp->matched_query); ?>'</td>
				</tr>
				<tr>
					<td>Global Query</td>
					<td><?php echo esc_html($wp_query->request); ?></td>
				</tr>
				<tr class="dev-collapsable">
					<td>Global Query Vars</td>
					<td><pre><?php print_r($wp_query->query_vars); ?></pre></td>
				</tr>
				<tr class="dev-collapsable">
					<td>All Rewrite Rules</td>
					<td>
						<ul>
						<?php 
							foreach($wp_rewrite->wp_rewrite_rules() as $rule => $query)
								printf("<li>'%s' => '%s'</li>", esc_html($rule), esc_html($query));
							?>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<?php 
	}
}
if(!is_admin()) {
	add_action('init', array(new WP_Theme_Devbar, 'init'));
}