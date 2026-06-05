import { dispatch } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import { addAction, addFilter } from '@wordpress/hooks';
import { store as editorStore } from '@wordpress/editor';

/**
 * Fires the action after the import
 * of the blocks is complete.
 *
 * @since 1.3.0
 *
 * @param {string} title   Post title.
 * @param {any[]}  content Imported blocks.
 *
 * @return {void}
 */
addAction( 'cbtj.afterImport', 'cbtj', async ( { blocks } ) => {
	const { editPost, savePost } = dispatch( editorStore ) as {
		editPost: any;
		savePost: any;
	};

	const footnote = blocks.filter( ( { name } ) => name === 'core/footnotes' );

	// Update post meta for core/footnotes after import.
	if ( footnote.length === 1 ) {
		const { footnotes } = JSON.parse( footnote[ 0 ].attributes );
		editPost( {
			meta: {
				footnotes,
			},
		} );
		await savePost();
	}
} );

/**
 * Recursively creates blocks from a block definition,
 * including nested innerBlocks at any depth.
 * @param blockDef
 */
const createBlockRecursive = ( blockDef: any ): any => {
	const { name, attributes, innerBlocks = [] } = blockDef;

	const parsedAttributes = attributes ? JSON.parse( attributes ) : {};
	const children = innerBlocks.map( createBlockRecursive );

	return createBlock( name, parsedAttributes, children );
};

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
		case 'core/accordion':
			blocks = innerBlocks.map( createBlockRecursive );
			break;

		default:
			break;
	}

	return blocks;
} );
