<?php

namespace KnowSchema\Schema;

class Graph_Builder {

	private $plugin_name;
	private $version;
	private $registry;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'schema/class-template-registry.php';
		$this->registry = new Template_Registry();
	}

	/**
	 * Output JSON-LD schema in the head
	 */
	public function output_schema() {
		if ( ! is_singular() && ! is_front_page() && ! is_home() ) {
			// MVP: Only target singular posts/pages + front page for now
			// return; 
		}

		// Cache key based on post ID and last modified
		$cache_key = '';
		if ( is_singular() ) {
			$post_id = get_the_ID();
			$cache_key = 'schema_graph_' . $post_id . '_' . get_the_modified_time( 'U', $post_id );
		} else {
			$cache_key = 'schema_graph_home';
		}

		$graph = false;
		// Try to get from transient (persistent cache)
		// We'll use transients for now as a simple caching mechanism
		// For high traffic sites, object cache is better, but transient works for all
		$graph = get_transient( 'ks_' . $cache_key );

		if ( false === $graph ) {
			$graph = $this->build_graph();
			if ( ! empty( $graph ) ) {
				set_transient( 'ks_' . $cache_key, $graph, 12 * HOUR_IN_SECONDS );
			}
		}

		if ( ! empty( $graph ) ) {
			echo '<script type="application/ld+json" class="knowschema-graph">' . "\n";
			echo wp_json_encode( $graph, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
			echo "\n" . '</script>' . "\n";
		}
	}

	/**
	 * Build the graph array
	 *
	 * @param array $overrides Optional overrides for simulation/preview
	 * @return array
	 */
	public function build_graph( $overrides = array() ) {
		$nodes = array();

		// 1. WebSite node (sitewide)
		$website_template = $this->registry->get_template( 'WebSite' );
		if ( $website_template ) {
			$nodes[] = $website_template->generate();
		}

		// 2. Contextual nodes (per page)
		if ( is_singular() || ! empty( $overrides['post_id'] ) ) {
			$post_id = ! empty( $overrides['post_id'] ) ? $overrides['post_id'] : get_the_ID();
			
			// Check for override in meta
			$selected_template = isset( $overrides['template'] ) ? $overrides['template'] : get_post_meta( $post_id, '_ks_schema_template', true );
			
			// Default logic if no override
			if ( empty( $selected_template ) ) {
				if ( is_front_page() && $post_id == get_option( 'page_on_front' ) ) {
					$selected_template = 'WebPage'; 
				} elseif ( get_post_type( $post_id ) === 'post' ) {
					$selected_template = 'Article'; // Simplification for MVP
				} else {
					$selected_template = 'WebPage';
				}
			}

			$template = $this->registry->get_template( $selected_template );
			if ( $template ) {
				$nodes[] = $template->generate( $post_id );
			}
			
			// Always include Breadcrumbs for singular
			$breadcrumbs_template = $this->registry->get_template( 'BreadcrumbList' );
			if ( $breadcrumbs_template ) {
				$nodes[] = $breadcrumbs_template->generate( $post_id );
			}

			// Include FAQ if it exists
			$faq_template = $this->registry->get_template( 'FAQPage' );
			if ( $faq_template ) {
				$faq_node = $faq_template->generate( $post_id );
				if ( ! empty( $faq_node ) ) {
					$nodes[] = $faq_node;
				}
			}

			// Author Person node (if Article)
			if ( $selected_template === 'Article' ) {
				$author_template = $this->registry->get_template( 'Person' );
				if ( $author_template ) {
					$nodes[] = $author_template->generate( get_post_field( 'post_author', $post_id ) );
				}
			}
		}

		// Organization node (Publisher) - sitewide for now
		$org_template = $this->registry->get_template( 'Organization' );
		if ( $org_template ) {
			$nodes[] = $org_template->generate();
		}

		return array(
			'@context' => 'https://schema.org',
			'@graph'   => $nodes,
		);
	}

}
