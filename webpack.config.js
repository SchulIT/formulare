const Encore = require('@symfony/webpack-encore');
const GlobImporter = require('node-sass-glob-importer');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('frontend', './assets/js/frontend.js')
    .addEntry('collection', './assets/js/collection.js')
    .addEntry('backend', './assets/js/backend.js')
    .addStyleEntry('simple', './assets/css/simple.scss')

    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    .enableSassLoader(function(options) {
        options.sassOptions.importer = GlobImporter();
    })
    .enablePostCssLoader()
    .enableVersioning(Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();
