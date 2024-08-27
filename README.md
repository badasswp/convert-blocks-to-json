# convert-blocks-to-json
Convert your WP blocks to JSON.

## Download

You can also get the latest version from any of our [release tags](https://github.com/badasswp/convert-blocks-to-json/releases).

## Why Convert Blocks to JSON?

This plugin offers a powerful solution for __exporting and importing WordPress blocks in JSON format__, making it easier to manage and reuse block structures across different projects. It is particularly beneficial for developers and site owners who are adopting a __Headless CMS__ approach on the front-end that is powered by tools like __React__, __Vue__ & so on.

With the ability to seamlessly transfer block data between environments, you can maintain consistency, streamline content updates, and enhance the efficiency of your development workflow.

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

## Development

### Setup

- Clone the repository.
- Make sure you have [Node](https://nodejs.org) installed on your computer.
- Run `npm install && npm run build` to build JS dependencies.
- For local development, you can use [Docker](https://docs.docker.com/install/) or [Local by Flywheel](https://localwp.com/).
