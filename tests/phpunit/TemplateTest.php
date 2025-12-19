<?php

namespace KnowSchema\Tests;

use PHPUnit\Framework\TestCase;
use KnowSchema\Schema\Templates\Template_Website;
use KnowSchema\Schema\Templates\Template_Organization;

class TemplateTest extends TestCase {

	public function test_website_template_structure() {
		// Mock dependencies handled in bootstrap or here if needed
		$template = new Template_Website();
		$output = $template->generate();

		$this->assertIsArray( $output );
		$this->assertEquals( 'WebSite', $output['@type'] );
		$this->assertEquals( 'https://example.com/', $output['url'] );
		$this->assertEquals( 'https://example.com/#website', $output['@id'] );
	}

	public function test_organization_template_structure() {
		$template = new Template_Organization();
		$output = $template->generate();

		$this->assertIsArray( $output );
		$this->assertEquals( 'Organization', $output['@type'] );
		$this->assertEquals( 'Test Site', $output['name'] );
		$this->assertEquals( 'https://example.com/#organization', $output['@id'] );
	}
}
