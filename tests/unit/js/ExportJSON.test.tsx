import { render } from '@testing-library/react';
import '@testing-library/jest-dom';

import ExportJSON from '../../src/components/ExportJSON';

jest.mock( '@wordpress/editor', () => ( {
	store: 'core/editor',
} ) );

jest.mock( '@wordpress/data', () => ( {
	select: jest.fn( ( storeName ) => {
		if ( storeName === 'core/editor' ) {
			return {
				getCurrentPostId: jest.fn( () => 1 ),
			};
		}
		return {};
	} ),
} ) );

jest.mock( '@wordpress/i18n', () => ( {
	__: jest.fn( ( text ) => text ),
} ) );

jest.mock( '@wordpress/components', () => ( {
	Button: jest.fn( ( { children } ) => {
		return (
			<button type="button" className="components-button is-primary">
				{ children }
			</button>
		);
	} ),
} ) );

describe( 'ExportJSON', () => {
	it( 'renders the component with correct text', () => {
		const { container } = render( <ExportJSON /> );

		// Expect Component to look like so:
		expect( container.innerHTML ).toBe(
			`<p>Export Blocks to JSON</p><button type="button" class="components-button is-primary">Export Blocks</button>`
		);
	} );
} );
