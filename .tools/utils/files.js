/**
 * External dependencies
 */
import { dirname, join, relative, resolve } from 'path';
import { readdir, readFile, stat } from 'fs/promises';
import { readFileSync } from 'fs';
import ignore from 'ignore';

let ignoreInstance;

/**
 * Returns configured ignore instance.
 *
 * @return {Object} ignore instance.
 */
const getIgnore = () => {
	if (!ignoreInstance) {
		ignoreInstance = ignore()
			.add(readFileSync(resolve('.gitignore')).toString())
			.add(readFileSync(resolve('.initignore')).toString());
	}

	return ignoreInstance;
};

/**
 * Recursively lists ALL files in given directory (this includes .git, node_modules etc).
 *
 * @param {string} dirPath - Start path.
 * @param {Array}  files   - List of files (this is used for the recursiveness).
 * @param {string} root    - Root file path.
 * @return {Promise}
 */
export const listFiles = async (dirPath = '.', files = [], root) => {
	const items = await readdir(dirPath);

	dirPath = resolve(dirPath);

	if (!root) {
		root = dirPath;
	}

	for (const item of items) {
		const newPath = join(dirPath, item);

		if (getIgnore().ignores(relative(root, newPath))) {
			continue;
		}

		if ((await stat(newPath)).isDirectory()) {
			files = await listFiles(newPath, files, root);
		} else {
			files.push(newPath);
		}
	}

	return files;
};
