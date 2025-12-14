import { render } from '@testing-library/react';
import '@testing-library/jest-dom';

import ViewJSON from '../src/components/ViewJSON';

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

jest.mock( '@wordpress/components', () => ( {
	Button: jest.fn( ( { children } ) => {
		return (
			<button type="button" className="components-button is-primary">
				{ children }
			</button>
		);
	} ),
} ) );

describe( 'ViewJSON', () => {
	beforeAll( () => {
		( global as unknown as { cbtj: any } ).cbtj = {
			baseUrl: 'https://example.com',
		};
	} );

	it( 'renders the component with correct text and link', () => {
		const { container } = render( <ViewJSON /> );

		expect( container ).toMatchSnapshot();
	} );
} );
