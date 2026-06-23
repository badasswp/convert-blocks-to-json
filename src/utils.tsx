import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { applyFilters } from '@wordpress/hooks';
import { store as editorStore } from '@wordpress/editor';

/**
 * Get Blocks.
 *
 * This function reaches out to the custom endpoint
 * and grabs the list of blocks available to the Post
 * with the current ID.
 *
 * @since 1.0.0
 *
 * @return {Promise<any[]>} Blocks.
 */
export const getBlocks = async (): Promise< any[] > => {
	const postID = ( select( editorStore ) as any ).getCurrentPostId();

	return await apiFetch( {
		path: `/cbtj/v1/${ postID }`,
	} );
};

/**
 * Get Import.
 *
 * This function reaches out to the import endpoint
 * and gets the list of JSON blocks.
 *
 * @since 1.0.1
 *
 * @param {any} attachment Attachment object.
 * @return {Promise<any>} Import.
 */
export const getImport = async ( attachment: any ): Promise< any > => {
	return await apiFetch( {
		path: '/cbtj/v1/import',
		method: 'POST',
		data: {
			...attachment,
		},
	} );
};

/**
 * Get Modal Params.
 *
 * This function is responsible for getting the
 * Modal params values for the WP Media Window Frame
 * displayed to the user.
 *
 * @since 1.0.1
 *
 * @return {any} Modal Params.
 */
export const getModalParams = (): any => {
	return {
		title: __( 'Select JSON File', 'convert-blocks-to-json' ),
		button: {
			text: __( 'Use JSON', 'convert-blocks-to-json' ),
		},
		multiple: false,
		library: {
			type: 'application/json',
		},
	};
};

/**
 * Get Inner Blocks.
 *
 * This function returns created version of the inner
 * blocks if they exist.
 *
 * @param  block
 * @param  block.name        Name of block.
 * @param  block.innerBlocks Inner blocks.
 *
 * @return {Array} Array of created blocks.
 */
export const getInnerBlocks = ( { name, innerBlocks } ): [] => {
	// Bail out, if empty.
	if ( ! innerBlocks.length ) {
		return [];
	}

	/**
	 * Filters the inner blocks depending on
	 * what type of block it is.
	 *
	 * @since 1.3.0
	 *
	 * @param {any[]}  innerBlocks Inner blocks.
	 * @param {string} name        Name of block.
	 *
	 * @return {Array}
	 */
	return applyFilters( 'cbtj.innerBlocks', innerBlocks, name ) as [];
};
