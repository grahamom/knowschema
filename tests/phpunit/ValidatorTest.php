<?php

namespace KnowSchema\Tests;

use PHPUnit\Framework\TestCase;
use KnowSchema\Schema\Validator;

class ValidatorTest extends TestCase {

	public function test_check_readiness_green() {
		$validator = new Validator();
		$data = array(
			'headline' => 'Test Headline',
			'datePublished' => '2023-01-01',
			'author' => array( 'name' => 'Author' ),
			'dateModified' => '2023-01-02',
			'image' => 'img.jpg',
			'publisher' => 'Pub'
		);
		
		$result = $validator->check_readiness( 'Article', $data );
		$this->assertEquals( 'green', $result['status'] );
		$this->assertEmpty( $result['missing_required'] );
	}

	public function test_check_readiness_red() {
		$validator = new Validator();
		$data = array(
			'headline' => 'Test Headline',
			// Missing author, datePublished
		);
		
		$result = $validator->check_readiness( 'Article', $data );
		$this->assertEquals( 'red', $result['status'] );
		$this->assertContains( 'author', $result['missing_required'] );
	}
}
