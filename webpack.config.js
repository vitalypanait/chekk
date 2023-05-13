const path = require('path')
const TerserWebpackPlugin = require('terser-webpack-plugin');
const {VueLoaderPlugin} = require('vue-loader')

module.exports = {
    mode: 'development',
    entry: './resources/assets/board/main.js',
    output: {
        path: path.resolve(__dirname, './public/dist'),
        clean: true,
    },
    module: {
        rules: [
            {test: /\.js$/, use: 'babel-loader'},
            {test: /\.vue$/, loader: 'vue-loader'},
            {test: /\.css$/, use: ['vue-style-loader', 'css-loader']},
        ]
    },
    plugins: [
        new VueLoaderPlugin()
    ],
    optimization: {
        runtimeChunk: 'single',
        splitChunks: {
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    chunks: 'all',
                },
            },
        },
        minimizer: [
            new TerserWebpackPlugin(),
        ]
    },
}