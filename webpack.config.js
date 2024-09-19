const Encore = require('@symfony/webpack-encore');
const CopyPlugin = require("copy-webpack-plugin");

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment('production');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addPlugin(new CopyPlugin({
        patterns: [
            {from: './assets/images', to: 'images'},
        ],
    }),)
    .addStyleEntry('email', './assets/styles/email/index.scss')
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(false)
    .enableVersioning(false)
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
