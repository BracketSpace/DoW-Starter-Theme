const defaultConfig = require('@micropackage/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	resolve: {
		roots: [path.resolve(__dirname)],
		alias: {
			config: path.resolve(__dirname, 'config/'),
			js: path.resolve(__dirname, 'src/assets/js'),
			scss: path.resolve(__dirname, 'src/assets/scss'),
			images: path.resolve(__dirname, 'src/assets/images'),
		},
	},
};
