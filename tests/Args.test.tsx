import '@testing-library/jest-dom';

import { getBlocks } from '../src/utils';

jest.mock( '@wordpress/data', () => ( {
  select: jest.fn( ( arg ) => {
    if ( arg === 'core/editor' ) {
      return {
        getCurrentPostId: jest.fn( () => 7 ),
      };
    }
    return {};
  } ),
} ) );

jest.mock( '@wordpress/api-fetch', () => jest.fn( ( options ) => {
  const { path } = options;
  if ( 'cbtj/v1/7' === path ) {
    return Promise.resolve(
      [
        {
          blockName: 'core/paragraph',
          innerHTML: '<p>Hello World</p>',
          innerBlocks: [],
        },
        {
          blockName: 'core/heading',
          innerHTML: '<h1>Hello World</h1>',
          innerBlocks: [],
        }
      ]
    );
  }
  return Promise.reject(new Error( 'Unknown path' ) );
} ) );

describe( 'Utilities', () => {
  it( 'gets the Blocks', async () => {
    const blocks = await getBlocks();
    expect( blocks ).toEqual(
      [
        {
          blockName: 'core/paragraph',
          innerHTML: '<p>Hello World</p>',
          innerBlocks: [],
        },
        {
          blockName: 'core/heading',
          innerHTML: '<h1>Hello World</h1>',
          innerBlocks: [],
        }
      ]
    );
  } );
} );
