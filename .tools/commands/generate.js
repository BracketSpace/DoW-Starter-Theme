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

export const command = [
    'generate <what>',
];

export const describe = 'generates theme.json or sass files with variables from config files.';

export const builder = (yargs) => {
    yargs.option('w', {
        alias: 'watch',
        boolean: true,
    });

    yargs.positional('what', {
        choices: ['theme-json', 'sass-vars'],
        describe: 'What to generate.'
    });
};

const internalHandler = async (what) => {
    const name = what === 'theme-json' ? 'theme.json' : 'Sass variables';

    const spinner = ora(`Generating ${name}...`).start();

    let errors = [];
    let notes = [];

    if (what === 'theme-json') {
        try {
            notes = await generateThemeJson();
        } catch (error) {
            errors.push(error);
        }
    }

    if (!errors.length) {
        spinner.succeed(`${name} generated.`);

        for (const note of notes) {
            signale.note(note);
        }
    } else {
        spinner.fail(`Generation of ${name} failed.`);

        for (const error of errors) {
            signale.error(error.message);
        }
    }
}

export const handler = async ({ what, watch }) => {
    if (watch) {
        console.log('Watching config files...');

        // Initial build.
        internalHandler(what);

        const callback = (path, event) => internalHandler(what);

        chokidar.watch('./config/*.json', {
            ignoreInitial: true,
        })
            .on('add', callback)
            .on('change', callback)
            .on('unlink', callback);
    } else {
        console.time('Executed in');

        await internalHandler(what);

        console.timeEnd('Executed in');
    }
};
