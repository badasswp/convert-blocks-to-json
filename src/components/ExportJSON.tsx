import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

import { getBlocks } from '../utils';

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
  const handleExport = async () => {
    const jsonBlocks = await getBlocks();
    const jsonString = JSON.stringify( jsonBlocks, null, 2 );
    const jsonURL    = URL.createObjectURL(
      new Blob(
        [jsonString],
        { type: 'application/json' }
      )
    );

    // Define Anchor.
    const a    = document.createElement( 'a' );
    a.href     = jsonURL;
    a.download = `convert-blocks-to-json-${Date.now()}.json`;

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
        onClick={ handleExport }
      >
        { __( 'Export Blocks', 'convert-blocks-to-json' ) }
      </Button>
    </>
  )
}

export default ExportJSON;
