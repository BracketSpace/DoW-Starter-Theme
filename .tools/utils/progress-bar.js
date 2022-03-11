/**
 * External dependencies
 */
import cliProgress from 'cli-progress';

/**
 * Creates a progress bar.
 *
 * @param {number}   total    - Total progress bar value.
 * @param {Function} callback - Async callback.
 * @return {Promise}
 */
export const progressBar = async (total, callback) => {
	const progressBar = new cliProgress.SingleBar(
		{
			clearOnComplete: true,
		},
		cliProgress.Presets.shades_classic
	);

	progressBar.start(total, 0);

	await callback(progressBar);

	progressBar.stop();
};
