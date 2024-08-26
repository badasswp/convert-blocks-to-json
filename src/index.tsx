import {
    __experimentalMainDashboardButton as MainDashboardButton,
} from '@wordpress/edit-post';
import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import { search } from '@wordpress/icons';
import { Button } from '@wordpress/components';
import { registerPlugin } from '@wordpress/plugins';

import './styles/app.scss';

/**
 * Convert Blocks To JSON.
 *
 * This function returns a JSX component that comprises
 * the WP Main dashboard, & the Convert Blocks To JSON button.
 *
 * @since 1.0.0
 *
 * @returns {JSX.Element}
 */
const ConvertBlocksToJSON = () => {
  const postID = select('core/editor').getCurrentPostId();

  const openModal = () => {
    window.location.href = `${cbtj.url}/wp-json/cbtj/v1/${postID}`
  }

  return (
    <MainDashboardButton>
      <Button
        id="cbtj"
        icon={search}
        onClick={openModal}
      />
    </MainDashboardButton>
  );
};

registerPlugin('convert-blocks-to-json', {
  render: ConvertBlocksToJSON,
});
