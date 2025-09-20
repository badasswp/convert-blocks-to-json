/* eslint-disable no-undef */
import '@testing-library/jest-dom';

jest.mock( '@wordpress/components', () => {
	return jest.requireActual( '@wordpress/components' );
} );
