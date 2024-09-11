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
 * @returns {mixed[]}
 */
export const getBlocks = async () => {
  const postID = select('core/editor').getCurrentPostId();

  return await apiFetch(
    {
      path: `cbtj/v1/${postID}`
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
    title: 'Select JSON File',
    button: {
      text: 'Use JSON'
    },
    multiple: false
  };
}
