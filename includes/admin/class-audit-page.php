<?php

namespace KnowSchema\Admin;

class Audit_Page {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function display() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Site Audit', 'knowschema' ); ?></h1>
			<p><?php _e( 'Overview of schema coverage and readiness across your site.', 'knowschema' ); ?></p>
			
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php _e( 'Page', 'knowschema' ); ?></th>
						<th><?php _e( 'Template', 'knowschema' ); ?></th>
						<th><?php _e( 'Status', 'knowschema' ); ?></th>
						<th><?php _e( 'Issues', 'knowschema' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$pages = get_posts( array( 'post_type' => array( 'post', 'page' ), 'posts_per_page' => 20 ) );
					if ( $pages ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'schema/class-graph-builder.php';
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'schema/class-validator.php';
						$graph_builder = new \KnowSchema\Schema\Graph_Builder( $this->plugin_name, $this->version );
						$validator = new \KnowSchema\Schema\Validator();

						foreach ( $pages as $p ) {
							$template = get_post_meta( $p->ID, '_ks_schema_template', true );
							if ( empty( $template ) ) { $template = 'Default'; }
							
							$graph = $graph_builder->build_graph( array( 'post_id' => $p->ID ) );
							$main_node = array();
							foreach ( $graph['@graph'] as $node ) {
								if ( isset( $node['@type'] ) && ( $node['@type'] === $template || ( $template === 'Default' && in_array( $node['@type'], array( 'Article', 'WebPage' ) ) ) ) ) {
									$main_node = $node;
									break;
								}
							}

							$readiness = $validator->check_readiness( $template === 'Default' ? $main_node['@type'] : $template, $main_node );
							
							echo '<tr>';
							echo '<td><strong>' . esc_html( $p->post_title ) . '</strong></td>';
							echo '<td>' . esc_html( $template === 'Default' ? $main_node['@type'] : $template ) . '</td>';
							echo '<td>' . $this->render_status_pill( $readiness['status'] ) . '</td>';
							echo '<td>';
							if ( ! empty( $readiness['missing_required'] ) ) {
								echo '<span style="color:#dc3232">' . sprintf( __( 'Missing: %s', 'knowschema' ), implode( ', ', $readiness['missing_required'] ) ) . '</span>';
							} elseif ( ! empty( $readiness['missing_recommended'] ) ) {
								echo '<span style="color:#ffb900">' . sprintf( __( 'Missing: %s', 'knowschema' ), implode( ', ', $readiness['missing_recommended'] ) ) . '</span>';
							} else {
								echo '-';
							}
							echo '</td>';
							echo '</tr>';
						}
					}
					?>
				</tbody>
			</table>
			<p><em><?php _e( 'Pro version expands audits to all pages and includes scheduled reports.', 'knowschema' ); ?></em></p>
		</div>
		<?php
	}

	private function render_status_pill( $status ) {
		$colors = array(
			'green' => '#46b450',
			'amber' => '#ffb900',
			'red'   => '#dc3232',
		);
		$color = isset( $colors[ $status ] ) ? $colors[ $status ] : '#ccc';
		return '<span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:' . $color . '; margin-right:5px;"></span>' . ucfirst( $status );
	}
}
