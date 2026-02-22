/**
 * Verify the plugin activates correctly in wp-env.
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';
import {loginAsAdmin} from "./helpers/ui/login";

test.describe( 'Plugin activation', () => {
	test( 'plugin is listed on the plugins page', async ( { admin, page } ) => {

		await loginAsAdmin(page);

		await admin.visitAdminPage( 'plugins.php' );

		const pluginRow = page.locator( 'tr[data-plugin="bh-wp-simple-calendar/bh-wp-simple-calendar.php"]' );
		await expect( pluginRow ).toBeVisible();

		const deactivateLink = pluginRow.locator( 'a:has-text("Deactivate")' );
		await expect( deactivateLink ).toBeVisible();
	} );
} );
