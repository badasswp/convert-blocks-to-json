import { __ } from '@wordpress/i18n';
import { dispatch } from '@wordpress/data';
import { Button } from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';
import { store as editorStore } from '@wordpress/editor';
import { store as noticeStore } from '@wordpress/notices';
import { store as blockEditorStore } from '@wordpress/block-editor';

import { getModalParams, getImport } from '../utils';

/**
 * Import JSON.
 *
 * This Component returns the Import JSON
 * label and button.
 *
 * @since 1.0.0
 * @since 1.0.1 Implement handleModal callback.
 *
 * @return {JSX.Element} Import JSON.
 */
const ImportJSON = (): JSX.Element => {
	/**
	 * Handles the Modal.
	 *
	 * This function is responsible for handling the
	 * WP Media Modal and its selection.
	 *
	 * @since 1.0.1
	 *
	 * @return {void}
	 */
	const handleModal = () => {
		const wpMediaModal = wp.media( getModalParams() );
		wpMediaModal.on( 'select', () => handleImport( wpMediaModal ) ).open();
	};

	/**
	 * Handles the Import.
	 *
	 * This function is responsible for handling the
	 * JSON import and its insertion.
	 *
	 * @since 1.0.1
	 *
	 * @param {Object} wpMediaModal
	 *
	 * @return {Promise<void>}
	 */
	const handleImport = async ( wpMediaModal: Object ): Promise< void > => {
		const attachment = wpMediaModal
			.state()
			.get( 'selection' )
			.first()
			.toJSON();

		try {
			// Get data.
			const { title, content } = await getImport( attachment );

			// Add title.
			dispatch( editorStore ).editPost( { title, status: 'publish' } );

			// Add content.
			content.forEach( ( { name, attributes, innerBlocks } ) => {
				attributes = JSON.parse( attributes );
				(
					dispatch( blockEditorStore ) as { insertBlocks: any }
				 ).insertBlocks(
					createBlock( name, { ...attributes }, innerBlocks )
				);
			} );

			// Save Post.
			await dispatch( editorStore ).savePost();
		} catch ( e ) {
			dispatch( noticeStore ).createWarningNotice( e.message );
		}
	};

	return (
		<>
			<p>{ __( 'Import Blocks by JSON', 'convert-blocks-to-json' ) }</p>
			<Button variant="primary" onClick={ handleModal }>
				{ __( 'Import Blocks', 'convert-blocks-to-json' ) }
			</Button>
		</>
	);
};

export default ImportJSON;
