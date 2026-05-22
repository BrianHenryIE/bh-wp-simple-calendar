/**
 * Verify the plugin activates correctly in wp-env.
 */
import { Page } from '@playwright/test';
import { test, expect } from '@wordpress/e2e-test-utils-playwright';
import { loginAsAdmin } from "./helpers/ui/login";

test.describe( 'Plugin activation', () => {

	async function goToPluginsPage( { admin, page } ): Promise< void > {
		await loginAsAdmin(page);
		await admin.visitAdminPage( 'plugins.php' );
	}

	test.beforeEach( async ( { browser, requestUtils } ) => {
		// const page = await browser.newPage();

		// Activate plugin
		await requestUtils.activatePlugin("simple-calendar");
	} );

	test( 'plugin is listed on the plugins page', async ( { admin, page } ) => {

		await goToPluginsPage( { admin, page } );

		const pluginRow = page.locator( 'tr[data-plugin="bh-wp-simple-calendar/bh-wp-simple-calendar.php"]' );
		await expect( pluginRow ).toBeVisible();
	} );

	test( 'plugin is active', async ( { admin, page } ) => {

		await goToPluginsPage( { admin, page } );

		const pluginRow = page.locator( 'tr[data-plugin="bh-wp-simple-calendar/bh-wp-simple-calendar.php"]' );
		const deactivateLink = pluginRow.locator( 'a:has-text("Deactivate")' );
		await expect( deactivateLink ).toBeVisible();
	} );
} );
