/**
 * External dependencies
 */
import chalk from 'chalk';
import { mapValues, pick, startCase } from 'lodash-es';

/**
 * Internal dependencies
 */
import { loadConfig, saveConfig } from './config.js';

const getColorPalette = async () =>
    Object.entries(await loadConfig('colors')).map(([slug, color]) => ({
        slug,
        color,
        name: startCase(slug)
    }));

const getLayoutSettings = async () =>
    mapValues(
        pick(await loadConfig('layout'), ['contentSize', 'wideSize']),
        (value) => `${value}px`
    );

export default async () => {
    let defaultConfig;
    let notes = [];

    try {
        defaultConfig = await loadConfig('theme');

        notes.push(`File created based on ${chalk.underline('theme.json')} from config dir.`);
    } catch (err) {
        defaultConfig = {
            version: 1,
        };

        notes.push(`${chalk.underline('theme.json')} file not found in config dir. Empty config created instead.`);
    }

    const [palette, layout] = await Promise.all([
        getColorPalette(),
        getLayoutSettings(),
    ]);

    const themeJson = {
        ...defaultConfig,
        settings: {
            ...defaultConfig.settings,
            color: {
                ...defaultConfig.settings?.color,
                palette,
            },
            layout
        }
    }

    saveConfig('theme', themeJson);

    return notes;
}
