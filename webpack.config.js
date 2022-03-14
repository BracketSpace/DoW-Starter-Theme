const defaultConfig = require('@micropackage/scripts/config/webpack.config');
const path = require('path');

defaultConfig.resolve.alias.config = path.resolve(__dirname, 'config/');
defaultConfig.resolve.alias['acf-blocks'] = path.resolve(
	__dirname,
	'src/blocks/'
);

module.exports = defaultConfig;
