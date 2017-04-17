var webpack = require('webpack');
var path = require('path');

module.exports = {
    entry: [
        './src/main.js'
    ],
    output: {
        path: path.resolve(__dirname, '../../public/backend/dist'),
        filename: 'app.js'
    },
    module: {
        rules: [
            // Loaders for CSS files
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            },
            // Loaders for font files
            {
                test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: "url-loader?limit=10000&mimetype=application/font-woff",
                query: {
                    outputPath: 'fonts/',
                    publicPath: '../fonts/' // That's the important part
                }
            },
            {
                test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: "file-loader",
                query: {
                    outputPath: 'fonts/',
                    publicPath: '../fonts/' // That's the important part
                }
            }
        ]
    },
    resolve: {
        alias: {
            jquery: "jquery/src/jquery",
            vue: "vue/dist/vue"
        }
    }
    // plugins: [
    //     new webpack.ProvidePlugin({
    //         $: "jquery",
    //         jQuery: "jquery"
    //     })
    // ]
};