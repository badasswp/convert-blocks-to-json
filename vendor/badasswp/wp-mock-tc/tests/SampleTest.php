<?php

namespace Badasswp\WPMockTC\Tests;

use Badasswp\WPMockTC\WPMockTestCase;

/**
 * @covers \Badasswp\WPMockTC\Tests\SampleClass::get_user_names_appended_to_post_id
 */
class SampleTest extends WPMockTestCase {
	private $sample_class;

	public function setUp(): void {
		parent::setUp();
		$this->sample_class = new SampleClass();
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	public function test_get_user_names_appended_to_post_id() {
		$this->assertSame(
			array(
				'John Doe - 1',
				'Cathryn Washington - 1',
				'Jack Foley - 1',
			),
			$this->sample_class->get_user_names_appended_to_post_id( 1 )
		);
	}
}

class SampleClass {
	public function get_user_names_appended_to_post_id( $post_id ) {
		// Safely type-cast Post ID.
		$id = absint( $post_id );

		// Get list of users.
		$users = array( 'John Doe', 'Cathryn Washington', 'Jack Foley' );

		// Get user names appended to Post ID.
		return array_map(
			function ( $arg ) use ( $id ) {
				return sprintf( '%s - %d', esc_html( $arg ), $id );
			},
			$users
		);
	}
}
