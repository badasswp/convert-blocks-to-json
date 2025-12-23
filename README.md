# convert-blocks-to-json
Convert your WP blocks to JSON.

[![Coverage Status](https://coveralls.io/repos/github/badasswp/convert-blocks-to-json/badge.svg?branch=master)](https://coveralls.io/github/badasswp/convert-blocks-to-json?branch=master)

<img src="https://github.com/user-attachments/assets/875acd91-b2ed-4832-8df7-9ebe013e9c35" alt="Convert Blocks to JSON" height="300">

## Download

Download from [WordPress plugin repository](https://wordpress.org/plugins/convert-blocks-to-json/).

You can also get the latest version from any of our [release tags](https://github.com/badasswp/convert-blocks-to-json/releases).

## Why Convert Blocks to JSON?

This plugin offers a powerful solution for __importing and exporting WordPress blocks in JSON format__, making it easy to move posts across different WP websites. It is also beneficial for WP engineers who are adopting a __Headless CMS__ approach and would like a way to be able to fetch data from the front-end using tools like __React__, __Vue__ & so on.

It's __simple__, yet very __powerful__!

https://github.com/user-attachments/assets/9dedf30f-9df0-4307-b634-cecef930a6e5

## Why can't I upload JSON files?

You might need to configure the `ALLOW_UNFILTERED_UPLOADS` option in your `wp-config.php` file like so:

```php
define( 'ALLOW_UNFILTERED_UPLOADS', true );
```

### Hooks

#### `cbtj_blocks`

This custom hook (filter) provides the ability to customise the block classes:

```php
add_filter( 'cbtj_blocks', [ $this, 'custom_blocks' ], 10 );

public function custom_blocks( $blocks ): array {
    $blocks[] = \YourNameSpace\YourCustomBlock::class;

    return $block;
}
```

**Parameters**

- blocks _`{Block[]}`_ By default, this is an array consisting of block classes.
<br/>

#### `cbtj_import_block`

This custom hook (filter) provides the ability to customise any block array during import:

```php
add_filter( 'cbtj_import_block', [ $this, 'custom_import_block' ], 10 );

public function custom_import_block( $block ): array {
    if ( 'core/image' !== $block['name'] ) {
        return $block;
    }

    // Get block attributes.
    $block['attributes'] = json_decode( $block['attributes'], true );

    // Set Caption using Post meta.
    $block['attributes']['caption'] = get_post_meta( get_the_ID(), 'featured_image_caption', true );

    // Encode attributes finally.
    $block['attributes'] = wp_json_encode( $block['attributes'] );

    return $block;
}
```

**Parameters**

- block _`{mixed[]}`_ By default, this would be a block array containing `name`, `originalContent`, `attributes` & `innerBlocks` key/value pairs.
<br/>

#### `cbtj_export_block`

This custom hook (filter) provides the ability to customise any block array during export:

```php
add_filter( 'cbtj_export_block', [ $this, 'custom_export_block' ], 10 );

public function custom_export_block( $block ): array {
    if ( 'core/image' !== $block['name'] ) {
        return $block;
    }

    // Set Caption using Post meta.
    $block['attributes']['caption'] = get_post_meta( get_the_ID(), 'featured_image_caption', true );

    return $block;
}
```

**Parameters**

- block _`{mixed[]}`_ By default, this would be a block array containing `name`, `content`, `filtered`, `attributes` & `innerBlocks` key/value pairs.
<br/>

#### `cbtj_rest_export`

This custom hook (filter) provides the ability to customise the REST response obtained:

```php
add_filter( 'cbtj_rest_export', [ $this, 'custom_rest_export' ], 10, 2 );

public function custom_rest_export( $response, $post_id ): array {
    $response['content'] = wp_parse_args(
        [
            'name'    => 'custom/post-meta-block',
            'content' => 'Lorem ipsum dolor sit amet...',
            'meta'    => [
                'address'   => get_post_meta( $post_id, 'address', true ),
                'telephone' => get_post_meta( $post_id, 'telephone', true ),
                'country'   => get_post_meta( $post_id, 'country', true ),
            ],
        ],
        $response['content']
    );

    return (array) $response;
}
```

**Parameters**

- response _`{mixed[]}`_ REST Response.
- post_id _`{int}`_ Post ID.
<br/>

#### `cbtj_rest_import`

This custom hook (filter) provides the ability to customise the REST import. For e.g To import only paragraphs, you can do:

```php
add_filter( 'cbtj_rest_import', [ $this, 'custom_rest_import' ], 10, 2 );

public function custom_rest_import( $import, $post_id ): array {
    return array_filter(
        $import,
        function( $block ) {
            return ( 'core/paragraph' === ( $block['name'] ?? '' ) );
        }
    );
}
```

**Parameters**

- import _`{mixed[]}`_ REST Import. By default this is an array of Blocks.
- post_id _`{int}`_ Post ID.
<br/>

#### `cbtj_rest_namespace`

This custom hook (filter) provides users the ability to customize the default REST namespace. For e.g.

```php
add_filter( 'cbtj_rest_namespace', [ $this, 'custom_namespace' ] );

public function custom_namespace( $namespace ): array {
    return 'my-custom-namespace/v1';
}
```

**Parameters**

- namespace _`{string}`_ REST Namespace. By default, this is a string which contains the Route namespace.
<br/>

---

## Contribute

Contributions are __welcome__ and will be fully __credited__. To contribute, please fork this repo and raise a PR (Pull Request) against the `master` branch.

### Pre-requisites

You should have the following tools before proceeding to the next steps:

- Composer
- Yarn
- Docker

To enable you start development, please run:

```bash
yarn start
```

This should spin up a local WP env instance for you to work with at:

```bash
http://cbtj.localhost:5478
```

You should now have a functioning local WP env to work with. To login to the `wp-admin` backend, please username as `admin` & password as `password`.

__Awesome!__ - Thanks for being interested in contributing your time and code to this project!
