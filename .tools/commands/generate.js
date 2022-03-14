/**
 * External dependencies
 */
import ora from 'ora';
import signale from 'signale';
import chokidar from 'chokidar';

/**
 * Internal dependencies
 */
import generateThemeJson from '../utils/generate-theme-json.js';
import generateSassVars from '../utils/generate-sass-vars.js';

export const command = ['generate <what>'];

export const describe =
	'generates theme.json or sass files with variables from config files.';

export const builder = (yargs) => {
	yargs.option('w', {
		alias: 'watch',
		boolean: true,
	});

	yargs.positional('what', {
		choices: ['theme-json', 'sass-vars'],
		describe: 'What to generate.',
	});
};

const displayNotes = (notes) => {
	if (!Array.isArray(notes)) {
		return;
	}

	for (const note of notes) {
		signale.note(note);
	}
};

const displayErrors = (errors) => {
	if (!Array.isArray(errors)) {
		return;
	}

	for (const error of errors) {
		const messages = error.message.split('|');

		for (const message of messages) {
			signale.error(message);
		}
	}
};

const internalHandler = async (what) => {
	const name = what === 'theme-json' ? 'theme.json' : 'Sass variables';

	const spinner = ora(`Generating ${name}...`).start();

	const errors = [];
	let notes;

	const func = what === 'theme-json' ? generateThemeJson : generateSassVars;

	try {
		notes = await func();
	} catch (error) {
		errors.push(error);
	}

	if (!errors.length) {
		spinner.succeed(`${name} generated.`);
		displayNotes(notes);
	} else {
		spinner.fail(`Generation of ${name} failed.`);
		displayErrors(errors);
	}
};

export const handler = async ({ what, watch }) => {
	if (watch) {
		// eslint-disable-next-line no-console
		console.log('Watching config files...');

		// Initial build.
		internalHandler(what);

		const callback = () => internalHandler(what);

		chokidar
			.watch('./config/*.json', {
				ignoreInitial: true,
			})
			.on('add', callback)
			.on('change', callback)
			.on('unlink', callback);
	} else {
		await internalHandler(what);
	}
};
