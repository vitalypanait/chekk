const path = require('path')
const { VueLoaderPlugin } = require('vue-loader')

module.exports = {
    mode: 'production',
    entry: './resources/assets/board/main.js',
    output: {
        path: path.resolve(__dirname, './public/dist'),
    },
    module: {
        rules: [
            { test: /\.js$/, use: 'babel-loader' },
            { test: /\.vue$/, loader: 'vue-loader'},
            { test: /\.css$/, use: ['vue-style-loader', 'css-loader']},
        ]
    },
    plugins: [
        new VueLoaderPlugin()
    ]
}