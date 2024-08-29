import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { Button } from '@wordpress/components';

/**
 * Export JSON.
 *
 * This Component returns the Export JSON
 * label and button.
 *
 * @since 1.0.0
 *
 * @returns {JSX.Element}
 */
const ExportJSON = () => {
  const postID = select('core/editor').getCurrentPostId();

  const handleExport = async () => {
    const blocks = await apiFetch(
      {
        path: `cbtj/v1/${postID}`
      }
    );

    const jsonString = JSON.stringify( blocks, null, 2 );
    const jsonURL = URL.createObjectURL(
      new Blob(
        [jsonString],
        { type: 'application/json' }
      )
    );

    // Define Anchor.
    const a    = document.createElement( 'a' );
    a.href     = jsonURL;
    a.download = `convert-blocks-to-json-${postID}.json`;

    // Fire Anchor.
    document.body.appendChild( a );
    a.click();

    // Clear Anchor.
    URL.revokeObjectURL( jsonURL );
    document.body.removeChild( a );
  }

  return (
    <>
      <p>{ __( 'Export Blocks to JSON', 'convert-blocks-to-json' ) }</p>
      <Button
        variant="primary"
        onClick={handleExport}
      >
        { __( 'Export Blocks', 'convert-blocks-to-json' ) }
      </Button>
    </>
  )
}

export default ExportJSON;
