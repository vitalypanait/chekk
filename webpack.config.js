const Encore = require('@symfony/webpack-encore');
const TerserWebpackPlugin = require('terser-webpack-plugin');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addEntry('main', './resources/assets/board/main.js')
    .setOutputPath('public/dist/')
    .setPublicPath('/dist')
    .enableVueLoader()
    .splitEntryChunks()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSingleRuntimeChunk()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(true)

let config = Encore.getWebpackConfig();

config.optimization.minimizer = [
    new TerserWebpackPlugin(),
];

// const path = require('path')
// const Encore = require('@symfony/webpack-encore');
// const TerserWebpackPlugin = require('terser-webpack-plugin');
// const { VueLoaderPlugin } = require('vue-loader')
//
// module.exports = {
//     // mode: 'development',
//     mode: 'production',
//     entry: './resources/assets/board/main.js',
//     output: {
//         path: path.resolve(__dirname, './public/dist'),
//         filename: '[name].[contenthash].js',
//         clean: true,
//     },
//     module: {
//         rules: [
//             { test: /\.js$/, use: 'babel-loader' },
//             { test: /\.vue$/, loader: 'vue-loader'},
//             { test: /\.css$/, use: ['vue-style-loader', 'css-loader']},
//         ]
//     },
//     plugins: [
//         new VueLoaderPlugin()
//     ],
//     optimization: {
//         runtimeChunk: 'single',
//         splitChunks: {
//             cacheGroups: {
//                 vendor: {
//                     test: /[\\/]node_modules[\\/]/,
//                     name: 'vendors',
//                     chunks: 'all',
//                 },
//             },
//         },
//         minimizer: [
//             new TerserWebpackPlugin(),
//         ]
//     },
// }

module.exports = Encore.getWebpackConfig();