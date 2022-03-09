const defaultConfig = require('@micropackage/scripts/config/webpack.config');
const path = require('path');

defaultConfig.resolve.alias.config = path.resolve(__dirname, 'config/');

module.exports = defaultConfig;
