import { select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const getBlocks = async () => {
  const postID = select('core/editor').getCurrentPostId();

  return await apiFetch(
    {
      path: `cbtj/v1/${postID}`
    }
  );
}
