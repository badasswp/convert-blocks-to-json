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
  const handleImport = (e: React.MouseEvent<HTMLButtonElement, MouseEvent>) => {
    e.preventDefault();
    const wpMediaModal = wp.media( getModalParams() );

    const doImport = () => {
      const attachment = wpMediaModal.state().get('selection').first().toJSON();
      if ( 'application/json' !== attachment.mime ) {
        console.log( 'Selected file:', attachment );
      }
    };

    wpMediaModal.on( 'select', doImport ).open();
  };

  return (
    <>
      <p>{ __( 'Import Blocks by JSON', 'convert-blocks-to-json' ) }</p>
      <Button
        variant="primary"
        onClick={ handleImport }
      >
        { __( 'Import Blocks', 'convert-blocks-to-json' ) }
      </Button>
    </>
  )
}
