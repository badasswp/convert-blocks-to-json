import { __ } from '@wordpress/i18n';
import { select } from '@wordpress/data';
import { Button } from '@wordpress/components';

/**
 * View JSON.
 *
 * This Component returns the View JSON
 * label and button.
 *
 * @since 1.0.0
 *
 * @return {JSX.Element} View JSON.
 */
const ViewJSON = (): JSX.Element => {
	const postID = select( 'core/editor' ).getCurrentPostId();

	return (
		<>
			<p>{ __( 'View JSON', 'convert-blocks-to-json' ) }</p>
			<a
				href={ `${ cbtj.url }/wp-json/cbtj/v1/${ postID }` }
				target="_blank"
				rel="noreferrer"
			>
				<Button variant="primary" onClick={ () => {} }>
					{ __( 'View JSON', 'convert-blocks-to-json' ) }
				</Button>
			</a>
		</>
	);
};

export default ViewJSON;
