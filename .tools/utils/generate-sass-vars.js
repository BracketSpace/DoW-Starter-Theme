/**
 * External dependencies
 */
import { mapValues } from 'lodash-es';
import exec from 'node-async-exec';

/**
 * Internal dependencies
 */
import { loadConfig, saveConfig } from './config.js';

export default async () => {
	const errors = [];

	const [colors, general, layout, typography] = await Promise.allSettled([
		loadConfig('colors'),
		loadConfig('general'),
		loadConfig('layout'),
		loadConfig('typography'),
	]);

	const data = mapValues(
		{
			colors,
			general,
			layout,
			typography,
		},
		(item) => {
			if (item.status === 'fulfilled') {
				return item.value;
			}
			errors.push(item.reason.message);
		}
	);

	if (errors.length) {
		throw new Error(errors.join('|'));
	}

	await saveConfig('.tmp/json/sass-vars', data);

	await exec({
		cmd: 'npx json-to-scss .tmp/json/sass-vars.json .tmp/scss/vars.scss',
	});
};
