const config = require('@micropackage/scripts/eslint-config.js');

module.exports = {
	...config,
	rules: {
		...config.rules,
		curly: 'error',
	},
};
