# convert-blocks-to-json
Convert your WP blocks to JSON.

<img src="https://github.com/user-attachments/assets/875acd91-b2ed-4832-8df7-9ebe013e9c35" alt="Convert Blocks to JSON" height="300">

## Download

You can also get the latest version from any of our [release tags](https://github.com/badasswp/convert-blocks-to-json/releases).

## Why Convert Blocks to JSON?

This plugin offers a powerful solution for __exporting and importing WordPress blocks in JSON format__, making it easier to manage and reuse block structures across different projects. It is particularly beneficial for developers and site owners who are adopting a __Headless CMS__ approach on the front-end that is powered by tools like __React__, __Vue__ & so on.

With the ability to seamlessly transfer block data between environments, you can maintain consistency, streamline content updates, and enhance the efficiency of your development workflow.

https://github.com/user-attachments/assets/9dedf30f-9df0-4307-b634-cecef930a6e5

### Hooks

#### `cbtj_rest_response`

This custom hook (filter) provides the ability to customise the REST response obtained:

```php
add_filter( 'cbtj_rest_response', [ $this, 'custom_rest_response' ], 10, 2 );

public function custom_rest_response( $response, $post_id ): array {
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

## Development

### Setup

- Clone the repository.
- Make sure you have [Node](https://nodejs.org) installed on your computer.
- Run `yarn install && yarn build` to build JS dependencies.
- For local development, you can use [Docker](https://docs.docker.com/install/) or [Local by Flywheel](https://localwp.com/).
