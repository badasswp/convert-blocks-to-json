import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

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
	const postID = select( 'core/editor' ).getCurrentPostId();

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
 * @param  attachment
 * @since 1.0.1
 *
 * @return {Promise<any>} Import.
 */
export const getImport = async ( attachment ): Promise< any > => {
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
	};
};
