import { addFilter } from '@wordpress/hooks';
import { createBlock } from '@wordpress/blocks';

/**
 * Filter the way we handle the innerBlocks
 * depending on the type of block.
 *
 * @since 1.3.0
 *
 * @param {any[]}  innerBlocks Inner Blocks.
 * @param {string} block       Name of block.
 *
 * @return {any[]}
 */
addFilter( 'cbtj.innerBlocks', 'cbtj', ( innerBlocks, block ) => {
	let blocks = [];

	switch ( block ) {
		case 'core/list':
		case 'core/quote':
		case 'core/details':
		case 'core/media-text':
		case 'core/gallery':
		case 'core/cover':
			blocks = innerBlocks.map( ( { name, attributes } ) =>
				createBlock( name, { ...JSON.parse( attributes ) } )
			);
			break;

		default:
			break;
	}

	return blocks;
} );
