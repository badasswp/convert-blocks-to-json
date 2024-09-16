import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

import { getModalParams } from '../utils';

/**
 * Import JSON.
 *
 * This Component returns the Import JSON
 * label and button.
 *
 * @since 1.0.0
 *
 * @returns {JSX.Element}
 */
const ImportJSON = (): JSX.Element => {
  const handleModal = () => {
    const wpMediaModal = wp.media( getModalParams() );
    wpMediaModal.on( 'select', () => handleImport( wpMediaModal ) ).open();
  }

  const handleImport = ( wpMediaModal ) => {
    const attachment = wpMediaModal.state().get( 'selection' ).first().toJSON();
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
