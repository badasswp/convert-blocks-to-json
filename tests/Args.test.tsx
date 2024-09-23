import '@testing-library/jest-dom';

import { getBlocks, getModalParams, getImport } from '../src/utils';

jest.mock( '@wordpress/data', () => ( {
  select: jest.fn( ( arg ) => {
    if ( 'core/editor' === arg ) {
      return {
        getCurrentPostId: jest.fn( () => 7 ),
      };
    }
    return {};
  } ),
} ) );

jest.mock( '@wordpress/api-fetch', () => jest.fn( ( options ) => {
  const { path, method } = options;

  if ( '/cbtj/v1/7' === path ) {
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

  if ( '/cbtj/v1/import' === path && 'POST' === method ) {
    return Promise.resolve(
      [
        {
          name: 'core/paragraph',
          attributes: {
            content: '<p>Hello World</p>'
          },
          innerBlocks: [],
        },
        {
          name: 'core/heading',
          attributes: {
            content: '<h1>Hello World</h1>'
          },
          innerBlocks: [],
        },
      ]
    );
  }

  return Promise.reject( new Error( 'Unknown path' ) );
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

  it( 'gets the Modal Params', () => {
    const params = getModalParams();
    expect( params ).toEqual(
      {
        title: 'Select JSON File',
        button: {
          text: 'Use JSON',
        },
        multiple: false
      }
    );
  } );

  it( 'gets the Import Blocks', async () => {
    const importBlocks = await getImport( {} );
    expect( importBlocks ).toEqual(
      [
        {
          name: 'core/paragraph',
          attributes: {
            content: '<p>Hello World</p>'
          },
          innerBlocks: [],
        },
        {
          name: 'core/heading',
          attributes: {
            content: '<h1>Hello World</h1>'
          },
          innerBlocks: [],
        },
      ]
    );
  } );
} );
