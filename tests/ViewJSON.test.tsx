import React from 'react';
import { render, screen } from '@testing-library/react';
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

describe( 'ViewJSON', () => {
	beforeAll( () => {
		global.cbtj = { url: 'https://example.com' };
	} );

	it( 'renders the component with correct text and link', () => {
		const { container } = render( <ViewJSON /> );

		// Expect Correct link is generated:
		const link = screen.getByRole( 'link' );
		expect( link ).toHaveAttribute( 'target', '_blank' );
		expect( link ).toHaveAttribute(
			'href',
			'https://example.com/wp-json/cbtj/v1/1'
		);

		// Expect Component to look like so:
		expect( container.innerHTML ).toBe(
			`<p>View JSON</p><a href="https://example.com/wp-json/cbtj/v1/1" target="_blank" rel="noreferrer"><button type="button" class="components-button is-primary">View JSON</button></a>`
		);
	} );
} );
