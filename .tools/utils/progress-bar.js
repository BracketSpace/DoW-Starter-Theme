/**
 * External dependencies
 */
import cliProgress from 'cli-progress';

/**
 * Creates a progress bar.
 *
 * @param {number}   total    - Total progress bar value.
 * @param {Function} callback - Async callback.
 * @return {Promise} Resolves, when the process completes.
 */
export const progressBar = async (total, callback) => {
	const bar = new cliProgress.SingleBar(
		{
			clearOnComplete: true,
		},
		cliProgress.Presets.shades_classic
	);

	bar.start(total, 0);

	await callback(bar);

	bar.stop();
};
