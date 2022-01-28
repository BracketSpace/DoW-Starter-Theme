/**
 * External dependencies
 */
import chalk from 'chalk';
import { mapValues, pick, startCase } from 'lodash-es';
import exec from 'node-async-exec';

/**
 * Internal dependencies
 */
import { loadConfig, saveConfig } from './config.js';

export default async () => {
    let defaultConfig;
    const errors = [];

    const [colors, general, typography] = await Promise.allSettled([
        loadConfig('colors'),
        loadConfig('general'),
        loadConfig('typography'),
    ]);

    const data = mapValues({
        colors,
        general,
        typography
    }, item => {
        if (item.status === 'fulfilled') {
            return item.value;
        } else {
            errors.push(item.reason.message);
        }
    });

    if (errors.length) {
        throw new Error(errors.join('|'));
    }

    await saveConfig('.tmp/json/sass-vars', data);

    await exec({
        cmd: 'npx json-to-scss .tmp/json/sass-vars.json .tmp/scss/vars.scss',
    });
}
