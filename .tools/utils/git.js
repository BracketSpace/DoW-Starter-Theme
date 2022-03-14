/**
 * External dependencies
 */
import spawn from 'cross-spawn';

const gitUrlPattern = /^(([A-Za-z0-9]+@|http(|s)\:\/\/)|(http(|s)\:\/\/[A-Za-z0-9]+@))([A-Za-z0-9.]+(:\d+)?)(?::|\/)([\d\/\w.-]+?)(\.git){1}$/;
const httpUrlPattern = /^(http(|s)\:\/\/)(([A-Za-z0-9]+)@)?([A-Za-z0-9.]+(:\d+)?)\//;
const sshUrlPattern = /^([A-Za-z0-9]+)@([A-Za-z0-9.]+(:\d+)?)(:|\/)?/;

export const isValidRepoUrl = (url) => !!url.match(gitUrlPattern);

export const isSshUrl = (url) => !!url.match(sshUrlPattern);

export const isHttpUrl = (url) => !!url.match(httpUrlPattern);

export const createSshUrl = (url) =>
	url.replace(httpUrlPattern, '$4@$5:').replace(/^@/, 'git@');

export const createHttpUrl = (url) => url.replace(sshUrlPattern, 'https://$2/');

export const getRepoUrls = (url) =>
	isSshUrl(url)
		? {
				http: createHttpUrl(url),
				ssh: url,
		  }
		: {
				http: url,
				ssh: createSshUrl(url),
		  };

export const testRepo = async (url) =>
	new Promise((resolve, reject) => {
		const child = spawn('git', ['ls-remote', url]);

		child.on('exit', (code) => (code === 0 ? resolve() : reject()));
	});

export const testRepoUrls = async ({ ssh, http }) => {
	const [isSshValid, isHttpValid] = await Promise.allSettled([
		testRepo(http),
		testRepo(ssh),
	]);

	return {
		ssh: isSshValid.status === 'fulfilled',
		http: isHttpValid.status === 'fulfilled',
	};
};
