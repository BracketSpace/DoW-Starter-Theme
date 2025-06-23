const defaultConfig = require('@micropackage/scripts/config/webpack.config');
const path = require('path');
const TsconfigPathsPlugin = require('tsconfig-paths-webpack-plugin');

defaultConfig.resolve.alias.config = path.resolve(__dirname, 'config/');
defaultConfig.resolve.alias['acf-blocks'] = path.resolve(
	__dirname,
	'src/blocks/'
);

defaultConfig.resolve.plugins = [new TsconfigPathsPlugin()];

module.exports = defaultConfig;
