# convert-blocks-to-json
Convert your WP blocks to JSON.

<img src="https://github.com/user-attachments/assets/875acd91-b2ed-4832-8df7-9ebe013e9c35" alt="Convert Blocks to JSON" height="300">

## Download

Download from [WordPress plugin repository](https://wordpress.org/plugins/convert-blocks-to-json/).

You can also get the latest version from any of our [release tags](https://github.com/badasswp/convert-blocks-to-json/releases).

## Why Convert Blocks to JSON?

This plugin offers a powerful solution for __importing and exporting WordPress blocks in JSON format__, making it easy to move posts across different WP websites. It is also beneficial for WP engineers who are adopting a __Headless CMS__ approach and would like a way to be able to fetch data from the front-end using tools like __React__, __Vue__ & so on.

It's __simple__, yet very __powerful__!

https://github.com/user-attachments/assets/9dedf30f-9df0-4307-b634-cecef930a6e5

### Hooks

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
add_filter( 'cbtj_rest_namespace', [ $this, 'custom_namespace' ], 10, 2 );

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
