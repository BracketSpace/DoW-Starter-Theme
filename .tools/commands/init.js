/**
 * External dependencies
 */
import { chain, filter, kebabCase, startCase } from 'lodash-es';
import { existsSync, readFileSync, rmSync } from 'fs';
import abbreviate from 'abbreviate';
import chalk from 'chalk';
import exec from 'node-async-exec';
import inquirer from 'inquirer';
import ora from 'ora';
import path from 'path';
import replaceInFile from 'replace-in-file';
import signale from 'signale';

/**
 * Internal dependencies
 */
import {
	getRepoUrls,
	isSshUrl,
	isValidRepoUrl,
	testRepoUrls,
} from '../utils/git.js';
import { listFiles } from '../utils/files.js';

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
 * @param {Object}           data             Field data from json config.
 * @param {string|undefined} data.default     Default value.
 * @param {string}           data.message     Question message.
 * @param {string}           data.name        Field name.
 * @param {string}           data.searchValue Value to search/replace, used as example.
 * @param {string}           data.label       Label.
 * @param {string}           newDefault       Field type.
 * @param {boolean}          addExample       Whether to add example to question message.
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
	const prefix = (isSingleWord
		? name
		: abbreviate(name, { length: 5, strict: false })
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
 * @return {Promise} Resolves when the theme info gets replaced, rejects on failure.
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
 * @return {Promise} Resolves when the theme info is replaced.
 */
const handleReplace = async () => {
	const data = await inquireBaseInfo();

	// eslint-disable-next-line no-console
	console.log(
		[
			chalk.bold('\nTheme informations:\n'),
			...getCollectedInfo(data),
			'\n',
		].join('\n')
	);

	const confirmedData = await confirmThemInfo(data);

	await replace(confirmedData);
};

/**
 * Validates repository url.
 *
 * @param {string} url Repository url.
 * @return {Promise} Resolves with object containing urls prepared to use in git and to replace in package.json.
 */
const validateRepoUrl = async (url) => {
	const checkSpinner = ora(`Checking the repository...`).start();
	const urls = getRepoUrls(url);
	const valid = await testRepoUrls(urls);
	const isSsh = isSshUrl(url);

	if ((isSsh && valid.ssh) || (!isSsh && valid.http)) {
		checkSpinner.succeed('Repository found.');

		if (!isSsh && valid.ssh) {
			signale.note(
				'You entered a http(s) URL, but SSH URL could be used.'
			);

			const { useSsh } = await inquirer.prompt([
				{
					type: 'confirm',
					name: 'useSsh',
					message: 'Do you wish to use SSH URL instead?',
				},
			]);

			return {
				repoUrl: useSsh ? urls.ssh : urls.http,
				packageJsonUrl: urls.http,
			};
		}

		return {
			repoUrl: isSsh ? urls.ssh : urls.http,
			packageJsonUrl: valid.http ? urls.http : urls.ssh,
		};
	}

	checkSpinner.fail('Could not connect to the remote repository.');

	const { what, newUrl } = await inquirer.prompt([
		{
			type: 'list',
			name: 'what',
			message: 'What to do?',
			choices: [
				'proceed',
				{
					name: 'edit the url',
					value: 'edit',
				},
			],
		},
		{
			default: url,
			message: 'Enter git reposoitory URL:',
			name: 'newUrl',
			type: 'input',
			validate: (value) =>
				isValidRepoUrl(value) || 'Please enter a valid git URL.',
			when: (answers) => answers.what === 'edit',
		},
	]);

	if (what === 'proceed') {
		return url;
	}

	return await validateRepoUrl(newUrl);
};

/**
 * Replaces repository url in package.json.
 *
 * @param {string} url Repository url.
 * @return {Promise} Resolves when the repo url gets replaced in package.json, rejects on failure.
 */
const replaceRepoUrlPackageJson = async (url) => {
	const spinner = ora(`Replacing repository url in package.json...`).start();

	try {
		await replaceInFile({
			files: path.resolve('package.json'),
			from: 'https://github.com/BracketSpace/DoW-Starter-Theme.git',
			to: url,
		});

		spinner.succeed('Repository url replaced in package.json');
	} catch (e) {
		spinner.fail('Could not replace repostory url in package.json');
		throw e;
	}
};

const initGitRepo = async (url, createBranch, initGitFlow) => {
	const spinner = ora(`Initializing Git repository...`).start();

	const commands = filter([
		'git init',
		`git remote add origin ${url}`,
		...(createBranch
			? [
					'git checkout -b develop',
					'git add .',
					initGitFlow
						? 'git flow init -fd'
						: 'git commit -m "Initial commit"',
			  ]
			: []),
	]);

	try {
		await exec({
			cmd: commands,
		});

		spinner.succeed('Git repository initialized.');
	} catch (e) {
		spinner.fail('Could not initialize Git repository.');
		throw e;
	}
};

/**
 * Pushes to the remote.
 *
 * @return {Promise} Resolves when pushing completes, rejects on failure.
 */
const pushToRemote = async () => {
	const spinner = ora('Pushing to remote...').start();

	try {
		await exec({
			cmd: 'git push -u origin develop',
		});

		spinner.succeed('Initial commit pushed to remote.');
	} catch (e) {
		spinner.fail('Could not push to remote.');
		throw e;
	}
};

/**
 * Removes existing .git directory.
 *
 * @return {Promise} Resolves when git dir is removed, rejects on failure.
 */
const removeGitDir = async () => {
	const spinner = ora('Removing .git directory...').start();
	const gitPath = path.resolve('.git');

	if (!existsSync(gitPath)) {
		spinner.succeed('Git directory does not exist, nothing to be removed.');
		return;
	}

	try {
		rmSync(gitPath, { recursive: true, force: true });
		spinner.succeed('Git directory removed.');
	} catch (e) {
		spinner.fail('Could not remove .git directory.');
		throw e;
	}
};

/**
 * Handles Git repository initialization process.
 *
 * @param {boolean} force Whether this task is forced by the `--git` param.
 * @return {Promise} Resolves when git initializationis complete.
 */
const handleGitInit = async (force) => {
	const { initGit, url, createBranch, initGitFlow } = await inquirer.prompt([
		...(!force
			? [
					{
						type: 'confirm',
						name: 'initGit',
						message: 'Initialize Git repository?',
						default: true,
					},
			  ]
			: []),
		{
			type: 'input',
			name: 'url',
			message: 'Enter git reposoitory URL:',
			validate: (value) =>
				isValidRepoUrl(value) || 'Please enter a valid git URL.',
			when: (answers) => force || answers.initGit,
		},
		{
			type: 'confirm',
			name: 'createBranch',
			message: 'Create develop branch?',
			default: true,
			when: (answers) => force || answers.initGit,
		},
		{
			type: 'confirm',
			name: 'initGitFlow',
			message: 'Initialize git flow with default settings?',
			default: true,
			when: (answers) => !!answers.createBranch,
		},
	]);

	if (!force && !initGit) {
		signale.note(
			'You can initialize Git repositiory later using --git flag.'
		);
		process.exit(0);
	}

	const { repoUrl, packageJsonUrl } = await validateRepoUrl(url);

	await replaceRepoUrlPackageJson(packageJsonUrl);
	await removeGitDir();
	await initGitRepo(repoUrl, createBranch, initGitFlow);
	await pushToRemote();
};

/**
 * Handles the commant. This is the entry point run by `yargs`.
 *
 * @param {Object}  params     CLI params.
 * @param {boolean} params.git Whether to only initialize the git repo.
 * @return {Promise} Resolves when the command process ends.
 */
export const handler = async ({ git }) => {
	try {
		if (!git) {
			await handleReplace();
		}

		await handleGitInit(git);
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
