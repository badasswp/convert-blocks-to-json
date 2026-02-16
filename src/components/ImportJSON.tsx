import { __ } from '@wordpress/i18n';
import { dispatch } from '@wordpress/data';
import { Button } from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';
import { store as editorStore } from '@wordpress/editor';
import { store as noticeStore } from '@wordpress/notices';
import { store as blockEditorStore } from '@wordpress/block-editor';

import { getModalParams, getImport, getInnerBlocks } from '../utils';

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
	 * @param {any} wpMediaModal
	 * @return {Promise<void>}
	 */
	const handleImport = async ( wpMediaModal: any ): Promise< void > => {
		const { editPost, savePost } = dispatch( editorStore ) as {
			editPost: any;
			savePost: any;
		};

		dispatch( noticeStore ).createNotice(
			'info',
			__(
				'Importing blocks into new Post. Please wait…',
				'convert-blocks-to-json'
			),
			{
				isDismissible: true,
				id: 'cbtj-info',
				type: 'snackbar',
			}
		);

		const attachment = wpMediaModal
			.state()
			.get( 'selection' )
			.first()
			.toJSON();

		try {
			// Get data.
			const { title, content } = await getImport( attachment );

			// Add title.
			editPost( { title, status: 'publish' } );

			// Add content.
			content.forEach( ( { name, attributes, innerBlocks } ) => {
				attributes = JSON.parse( attributes );
				(
					dispatch( blockEditorStore ) as { insertBlocks: any }
				 ).insertBlocks(
					createBlock(
						name,
						{ ...attributes },
						getInnerBlocks( { name, innerBlocks } )
					)
				);
			} );

			// Save Post.
			await savePost();
			dispatch( noticeStore ).removeNotice( 'cbtj-info' );
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
