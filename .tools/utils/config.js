/**
 * External dependencies
 */
import { existsSync } from 'fs';
import { readFile, writeFile } from 'fs/promises';
import { resolve } from 'path';
import chalk from 'chalk';

export const getPath = (name) => resolve('./config', name);

export const loadConfig = async (name) => {
    const filename = `${name}.json`;
    const path = getPath(filename);

    if (!existsSync(path)) {
        throw new Error(`Config file ${chalk.underline(filename)} does not exist.`);
    }

    return JSON.parse(await readFile(path));
}

export const saveConfig = async (name, data) => writeFile(resolve(`${name}.json`), JSON.stringify(data, null, 2));
