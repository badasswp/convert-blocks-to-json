import { __ } from '@wordpress/i18n';
import { dispatch } from '@wordpress/data';
import { Button } from '@wordpress/components';
import { createBlock } from '@wordpress/blocks';

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
 * @returns {JSX.Element}
 */
const ImportJSON = (): JSX.Element => {
  const handleModal = () => {
    const wpMediaModal = wp.media( getModalParams() );
    wpMediaModal.on( 'select', () => handleImport( wpMediaModal ) ).open();
  }

  const handleImport = async ( wpMediaModal ) => {
    const attachment = wpMediaModal.state().get('selection').first().toJSON();
    const jsonImport = await getImport( attachment ) as any[];

    jsonImport.forEach( ( { name, attributes, innerBlocks } ) => {
      attributes = JSON.parse( attributes );
      ( dispatch('core/block-editor') as { insertBlocks: any } ).insertBlocks(
        createBlock( name, { ...attributes }, innerBlocks )
      );
    } );
  };

  return (
    <>
      <p>{ __( 'Import Blocks by JSON', 'convert-blocks-to-json' ) }</p>
      <Button
        variant="primary"
        onClick={ handleModal }
      >
        { __( 'Import Blocks', 'convert-blocks-to-json' ) }
      </Button>
    </>
  )
}

export default ImportJSON;
