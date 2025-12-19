<?php

namespace KnowSchema\Schema;

class Template_Registry {

	private $templates = array();

	public function __construct() {
		$this->register_defaults();
	}

	private function register_defaults() {
		$files = array(
			'WebSite'        => 'class-template-website.php',
			'WebPage'        => 'class-template-webpage.php',
			'Article'        => 'class-template-article.php',
			'Organization'   => 'class-template-organization.php', // Note: Briefing listed it under templates folder but maybe didn't explicitly name the file "class-template-organization.php", implied by "Organization or Person" entities. I will create it.
			'Person'         => 'class-template-person.php',
			'BreadcrumbList' => 'class-template-breadcrumbs.php',
			'FAQPage'        => 'class-template-faq.php',
			'Review'         => 'class-template-review.php',
			'Event'          => 'class-template-event.php',
			'Product'        => 'class-template-product.php',
		);

		foreach ( $files as $key => $file ) {
			$path = plugin_dir_path( __FILE__ ) . 'templates/' . $file;
			if ( file_exists( $path ) ) {
				require_once $path;
				$class_name = 'KnowSchema\Schema\Templates\Template_' . $key;
				if ( class_exists( $class_name ) ) {
					$this->templates[ $key ] = new $class_name();
				}
			}
		}
	}

	public function get_template( $key ) {
		return isset( $this->templates[ $key ] ) ? $this->templates[ $key ] : null;
	}

}
