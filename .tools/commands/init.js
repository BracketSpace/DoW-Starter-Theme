/**
 * External dependencies
 */
import { chain, filter, kebabCase, startCase } from 'lodash-es';
import { readFileSync } from 'fs';
import abbreviate from 'abbreviate';
import chalk from 'chalk';
import inquirer from 'inquirer';
import ora from 'ora';
import path from 'path';
import replaceInFile from 'replace-in-file';
import signale from 'signale';
import yargs from 'yargs';

/**
 * Internal dependencies
 */
import { listFiles } from '../utils/files.js';
import { progressBar } from '../utils/progress-bar.js';

export const command = ['init'];

export const describe =
	'replaces theme information (name, slug etc.) and initializes git repository.';

export const builder = (yargs) => {
	yargs.option('g', {
		alias: 'git',
		boolean: true,
		describe: 'Only initializes git repository.',
	});
};

const replaceData = JSON.parse(
	readFileSync(new URL('../data/replace.json', import.meta.url))
);

/**
 * Maps data to question params.
 *
 * @param {Object}           data             - Field data from json config.
 * @param {string|undefined} data.default     - Default value.
 * @param {string}           data.message     - Question message.
 * @param {string}           data.name        - Field name.
 * @param {[type]}           data.searchValue - Value to search/replace, used as example.
 * @param                    data.label
 * @param {string}           newDefault       - Field type.
 * @param {boolean}          addExample       - Whether to add example to question message.
 * @return {Object} Question params.
 */
const dataToQuestion = (
	{ default: defaultValue, label, message, name, searchValue },
	newDefault,
	addExample = true
) => ({
	default: newDefault || defaultValue,
	message: addExample ? `${message} (e.g. ${searchValue}):` : message,
	name,
	type: 'input',
	validate:
		!newDefault && !defaultValue
			? (value) =>
					!!value.length ||
					`Please provide a valid ${label.toLowerCase()}.`
			: undefined,
});

/**
 * Returns questions for given field names or skipping given field names.
 *
 * @param {Array}  names    Field names to get (or skip) questions for.
 * @param {Object} defaults Optional default values.
 * @return {Array}           Questions list.
 */
const getQuestions = (names = false, defaults = {}) =>
	chain(replaceData)
		.filter((item) => !names || names.includes(item.name))
		.map((item) => dataToQuestion(item, defaults[item.name]))
		.value();

/**
 * Returns array of information about all collected answers.
 *
 * @param {Object} data Collected data.
 * @return {Array}       Info about collected data.
 */
const getCollectedInfo = (data) =>
	filter(
		replaceData.map((item) =>
			data[item.name]
				? `${item.label}: ${chalk.bold.green(data[item.name])}`
				: null
		)
	);

/**
 * Inquires the basic them information: theme name and vendor name.
 *
 * @param {Object} data Object with collected data. It allows to filter the fields if their values were already
 *                      collected.
 *
 * @return {Promise} Promise, which resolves with theme info data object if data provided.
 */
const inquireBaseInfo = async (data = {}) => {
	const answers = {
		...data,
		...(await inquirer.prompt(
			getQuestions(
				['name', 'vendor'].filter(
					(fieldName) => !Object.keys(data).includes(fieldName)
				)
			)
		)),
	};

	const vendor = answers.vendor.toLowerCase();

	if (!answers.name) {
		signale.error('Please provide a valid theme name.');
		return inquireBaseInfo({ vendor });
	}

	const name = startCase(answers.name);
	const isSingleWord = name.split(' ').length === 1;
	const isLong = name.length > 17;
	const prefix = (
		isSingleWord ? name : abbreviate(name, { length: 5, strict: false })
	).toLowerCase();
	const slug = isLong ? prefix : kebabCase(name);
	const composerName = `${vendor}/${slug}`;
	const namespace = isLong ? startCase(slug) : name.replace(/\s/g, '');

	return {
		name,
		slug,
		composerName,
		namespace,
		prefix,
	};
};

/**
 * Lists collected theme information and asks to confirm or edit entries.
 *
 * @param {Object} data - Data collected in previous run.
 * @return {Promise} Resolves with collected data after the `proceed` option is selected.
 */
const confirmThemInfo = async (data) => {
	const { what } = await inquirer.prompt([
		{
			type: 'list',
			name: 'what',
			message: 'What to do?',
			choices: [
				'proceed',
				...filter(
					replaceData.map((item) =>
						data[item.name]
							? {
									name: `Change ${item.label.toLowerCase()} (${chalk.bold.green(
										data[item.name]
									)})`,
									value: item.name,
									short: `Change ${item.label.toLowerCase()}`,
							  }
							: null
					)
				),
			],
		},
	]);

	if (what === 'proceed') {
		return data;
	}

	const answers = await inquirer.prompt(getQuestions([what], data));

	return confirmThemInfo({
		...data,
		...answers,
	});
};

/**
 * Replaces the given strings in each file of the project.
 *
 * @param {Object} data Values to be used as replacements.
 * @return {Promise}
 */
const replace = async (data) => {
	let files;

	const listingSpinner = ora(`Listing files...`).start();

	try {
		files = await listFiles(path.resolve('.'));

		listingSpinner.succeed(
			`Files listed correctly. ${files.length} files found.`
		);
	} catch (e) {
		listingSpinner.fail(`Listing files failed.`);
		throw e;
	}

	const searchReplaceData = replaceData
		.map((item) => ({
			search: item.searchValue,
			replace: data[item.name],
		}))
		.filter((item) => !!item.replace);

	const replaceSpinner = ora(`Replacing theme info...`).start();

	try {
		const results = await replaceInFile({
			files,
			from: searchReplaceData.map((item) => new RegExp(item.search, 'g')),
			to: searchReplaceData.map((item) => item.replace),
		});

		const changedFiles = results.filter((item) => item.hasChanged).length;

		replaceSpinner.succeed(`Theme info replaced in ${changedFiles} files.`);
	} catch (e) {
		replaceSpinner.fail(`Replacing theme info failed.`);
		throw e;
	}
};

/**
 * Handles the process of gathering the theme info and replacing strings in project files.
 *
 * @return {Promise}
 */
const handleReplace = async () => {
	const data = await inquireBaseInfo();

	console.log(
		[
			chalk.bold('\nTheme informations:\n'),
			...getCollectedInfo(data),
			'\n',
		].join('\n')
	);

	const confirmedData = await confirmThemInfo(data);

	await replace(data);
};

/**
 * Handles Git repository initialization process.
 *
 * @return {Promise}
 */
const handleGitInit = async () => {
	console.log('init git.');
};

/**
 * Handles the commant. This is the entry point run by `yargs`.
 *
 * @param  {Object}  params     CLI params.
 * @param  {boolean} params.git Whether to only initialize the git repo.
 * @return {Promise}
 */
export const handler = async ({ git }) => {
	try {
		if (!git) {
			await handleReplace();
		}

		await handleGitInit();
	} catch (e) {
		if (e.isTtyError) {
			signale.error(
				'`tools init` command needs to be run in an interactive environment.'
			);
		} else {
			signale.fatal(e);
		}

		process.exit(1);
	}
};
