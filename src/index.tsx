import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { Fragment } from '@wordpress/element';
import { PanelBody, Button } from '@wordpress/components';
import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';

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

  return (
    <Fragment>
      <PluginSidebarMoreMenuItem
        target="cbtj-sidebar"
        icon="editor-code"
      >
        { __( 'Convert Blocks to JSON' ) }
      </PluginSidebarMoreMenuItem>
      <PluginSidebar
        name="cbtj-sidebar"
        title={ __( 'Convert Blocks to JSON' ) }
        icon="editor-code"
      >
        <PanelBody>
          <div id="cbtj">
            <p>{ __( 'View JSON' ) }</p>
            <a href={`${cbtj.url}/wp-json/cbtj/v1/${postID}`} target="_blank">
              <Button
                variant="primary"
                onClick={ () => { } }
              >
                { __( 'View JSON' ) }
              </Button>
            </a>

            <hr />

            <p>{ __( 'Import Blocks to JSON' ) }</p>
            <Button
              variant="primary"
              onClick={ () => { } }
            >
              { __( 'Import Blocks' ) }
            </Button>
          </div>
        </PanelBody>
      </PluginSidebar>
    </Fragment>
  );
};

registerPlugin( 'convert-blocks-to-json', {
  render: ConvertBlocksToJSON,
} );

