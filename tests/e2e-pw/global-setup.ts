import { request, type FullConfig } from '@playwright/test';
import { RequestUtils } from '@wordpress/e2e-test-utils-playwright';
import * as fs from 'fs';
import * as path from 'path';

async function globalSetup( config: FullConfig ) {
	const { storageState, baseURL } = config.projects[ 0 ].use;
	const storageStatePath =
		typeof storageState === 'string' ? storageState : path.join(
				__dirname,
				'.auth',
				'storage-state.json'
			);

	// Ensure the auth directory exists.
	fs.mkdirSync( path.dirname( storageStatePath ), { recursive: true } );

	const requestContext = await request.newContext( {
		baseURL,
	} );

	const requestUtils = new RequestUtils( requestContext, {
		storageStatePath,
	} );

	await requestUtils.setupRest();

	await requestContext.dispose();
}

export default globalSetup;
