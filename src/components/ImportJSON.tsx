import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

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
const ImportJSON = () => {
  const handleImport = () => {
    const customUploader = wp.media(
      {
        title: 'Select JSON File',
        button: {
          text: 'Use JSON'
        },
        multiple: false,
        library: {
          type: 'json'
        }
      }
    );
  }
  return (
    <>
      <p>{ __( 'Import Blocks by JSON', 'convert-blocks-to-json' ) }</p>
      <Button
        variant="primary"
        onClick={ () => { } }
      >
        { __( 'Import Blocks', 'convert-blocks-to-json' ) }
      </Button>
    </>
  )
}

export default ImportJSON;
