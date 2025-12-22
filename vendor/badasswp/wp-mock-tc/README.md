# WPMockTestCase

__WPMockTestCase__ provides a __solid base test case class__ that centralizes common WordPress mocks so you don’t have to repeat them in every test.

## Getting Started

Install directly from __Packagist__ using Composer like so:

```bash
composer require badasswp/wp-mock-tc
```

## Setup & TearDown Methods

Load `parent` methods correctly by running `setUp` and `tearDown` methods like so:

```php
use Badasswp\WPMockTC\WPMockTestCase;

class SampleTest extends WPMockTestCase {
    public function setUp(): void {
        parent::setUp();
    }

    public function tearDown(): void {
        parent::tearDown();
    }
}
```

## Demo Sample

The example below shows a class called `SampleClass` which contains a public method that appends the list of names to the `post_id` param.

We can simply write our test case and allow __WPMockTestCase__ take care of the mock definitions under the hood like so:

```php
use Badasswp\WPMockTC\WPMockTestCase;

class SampleClass {
    public function get_user_names_appended_to_post_id( $post_id ) {
        // Safely type-cast Post ID.
        $id = absint( $post_id );

        // Get list of users.
        $users = [ 'John Doe', 'Cathryn Washington', 'Jack Foley' ];

        // Get user names appended to Post ID.
        return array_map(
            function( $arg ) use( $id ) {
                return sprintf( '%s - %d', esc_html( $arg ), $id );
            },
            $users
        );
    }
}

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
            [
                'John Doe - 1',
                'Cathryn Washington - 1',
                'Jack Foley - 1'
            ],
            $this->sample_class->get_user_names_appended_to_post_id( 1 )
        );
    }
}
```

Notice that in our test, we are not mocking the WP functions `absint` and `esc_html`, __WPMockTestCase__ handles that for us under the hood, so we have one less thing to worry about.

## Caveat

At the moment, __WPMockTestCase__ DOES NOT mock all of WP functions. So you may have to do some of your own mocking in your test cases.
