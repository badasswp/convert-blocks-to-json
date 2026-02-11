import { test, expect } from '@wordpress/e2e-test-utils-playwright';

export async function createNewPost( page ) {
	await page.goto( '/wp-admin/post-new.php' );
	await page.waitForSelector( '.edit-post-layout' );
}

test.describe( 'Convert Blocks to JSON', () => {
	test.beforeEach( async ( { page } ) => {
		createNewPost( page );
	} );

	test( 'displays the plugin icon', async ( { page } ) => {
		const closeIcon = page.getByRole( 'button', { name: 'Close' } );

		await expect( closeIcon ).toBeVisible();
		await closeIcon.click();

		const pluginIcon = page.getByRole( 'button', {
			name: 'Convert Blocks to JSON',
		} );

		await expect( pluginIcon ).toBeVisible();
	} );

	test( 'displays the view, import & export buttons', async ( { page } ) => {
		const pluginIcon = page.getByRole( 'button', {
			name: 'Convert Blocks to JSON',
		} );

		await expect( pluginIcon ).toBeVisible();
		await pluginIcon.click();

		await expect(
			page.getByRole( 'button', { name: 'View JSON' } )
		).toBeVisible();
		await expect(
			page.getByRole( 'button', { name: 'Import Blocks' } )
		).toBeVisible();
		await expect(
			page.getByRole( 'button', { name: 'Export Blocks' } )
		).toBeVisible();
	} );

	test( 'displays the plugin button labels', async ( { page } ) => {
		const pluginIcon = page.getByRole( 'button', {
			name: 'Convert Blocks to JSON',
		} );

		await expect( pluginIcon ).toBeVisible();
		await pluginIcon.click();

		await expect(
			page.getByRole( 'paragraph' ).filter( { hasText: 'View JSON' } )
		).toBeVisible();
		await expect( page.getByText( 'Import Blocks by JSON' ) ).toBeVisible();
		await expect( page.getByText( 'Export Blocks to JSON' ) ).toBeVisible();
	} );

	test( 'opens up the WP media library modal', async ( { page } ) => {
		const pluginIcon = page.getByRole( 'button', {
			name: 'Convert Blocks to JSON',
		} );

		await expect( pluginIcon ).toBeVisible();
		await pluginIcon.click();

		const importButton = page.getByRole( 'button', {
			name: 'Import Blocks',
		} );

		await expect( importButton ).toBeVisible();
		await importButton.click();

		await expect(
			page.getByRole( 'heading', { name: 'Select JSON File' } )
		).toBeVisible();
		await expect(
			page.getByRole( 'heading', { name: 'Drop files to upload' } )
		).toBeVisible();
	} );

	test( 'views the JSON for the current post', async ( { page } ) => {
		const pluginIcon = page.getByRole( 'button', {
			name: 'Convert Blocks to JSON',
		} );

		await expect( pluginIcon ).toBeVisible();
		await pluginIcon.click();

		const postTitle = page
			.locator( 'iframe[name="editor-canvas"]' )
			.contentFrame()
			.getByRole( 'textbox', { name: 'Add title' } );

		await expect( postTitle ).toBeVisible();
		await postTitle.fill( 'test' );

		const publishButton = page.getByRole( 'button', {
			name: 'Publish',
			exact: true,
		} );
		await expect( publishButton ).toBeVisible();
		await publishButton.click();

		const confirmPublishButton = page
			.getByLabel( 'Editor publish' )
			.getByRole( 'button', { name: 'Publish', exact: true } );
		await expect( confirmPublishButton ).toBeVisible();
		await confirmPublishButton.click();

		const closePublishIcon = page.getByRole( 'button', {
			name: 'Close panel',
		} );
		await expect( closePublishIcon ).toBeVisible();
		await closePublishIcon.click();

		const viewButton = page.getByRole( 'button', {
			name: 'View JSON',
		} );
		await expect( viewButton ).toBeVisible();
		await viewButton.click();
	} );
} );
