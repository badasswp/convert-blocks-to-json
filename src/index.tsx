import { __ } from '@wordpress/i18n';
import { Fragment } from '@wordpress/element';
import { PanelBody, Button } from '@wordpress/components';
import { registerPlugin } from '@wordpress/plugins';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post';

import ViewJSON from './components/ViewJSON';
import ImportJSON from './components/ImportJSON';
import ExportJSON from './components/ExportJSON';

import './styles/app.scss';

/**
 * Convert Blocks To JSON.
 *
 * This function returns a JSX component that comprises
 * the Plugin Sidebar and the JSON components.
 *
 * @since 1.0.0
 *
 * @returns {JSX.Element}
 */
const ConvertBlocksToJSON = () => {
  return (
    <Fragment>
      <PluginSidebarMoreMenuItem
        target="cbtj-sidebar"
        icon="editor-code"
      >
        { __( 'Convert Blocks to JSON', 'convert-blocks-to-json' ) }
      </PluginSidebarMoreMenuItem>
      <PluginSidebar
        name="cbtj-sidebar"
        title={ __( 'Convert Blocks to JSON', 'convert-blocks-to-json' ) }
        icon="editor-code"
      >
        <PanelBody>
          <div id="cbtj">
            <ul>
              <li>
                <ViewJSON />
              </li>
              <li>
                <ImportJSON />
              </li>
              <li>
                <ExportJSON />
              </li>
            </ul>
          </div>
        </PanelBody>
      </PluginSidebar>
    </Fragment>
  );
};

registerPlugin( 'convert-blocks-to-json', {
  render: ConvertBlocksToJSON,
} );

