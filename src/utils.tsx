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
 * @returns {Promise<any[]>}
 */
export const getBlocks = (): Promise<any[]> => {
  const postID = select('core/editor').getCurrentPostId();

  return apiFetch(
    {
      path: `/cbtj/v1/${postID}`
    }
  );
}

/**
 * Get Import.
 *
 * This function reaches out to the import endpoint
 * and gets the list of JSON blocks.
 *
 * @since 1.0.1
 *
 * @returns {Promise<any[]>}
 */
export const getImport = ( attachment ): Promise<any[]> => {
  return apiFetch(
    {
      path: '/cbtj/v1/import',
      method: 'POST',
      data: {
        ...attachment
      },
    }
  );
}

/**
 * Get Modal Params.
 *
 * This function is responsible for getting the
 * Modal params values for the WP Media Window Frame
 * displayed to the user.
 *
 * @since 1.0.1
 *
 * @returns {Object} Modal Params.
 */
export const getModalParams = () => {
  return {
    title: __( 'Select JSON File', 'convert-blocks-to-json' ),
    button: {
      text: __( 'Use JSON', 'convert-blocks-to-json' )
    },
    multiple: false
  };
}
