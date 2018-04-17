const webpack = require("webpack");
const path = require("path");
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');

module.exports = {
    context: path.resolve(__dirname),
    entry: {
        // "bootstrap-loader",
        web: "./src/wallet/WebBundle/Resources/public/web/index.js",
        mobile: "./src/wallet/WebBundle/Resources/public/mobile/index.js"
    },
    output: {
        path: path.resolve(__dirname, './web/build'),
        filename: '[name].js'
    },
    module: {
        loaders: [
            {test: /\.(woff2?|svg)$/, loader: 'url-loader?limit=10000'},
            {test: /\.(ttf|eot)$/, loader: 'file-loader'},
        ],
        rules: [
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: 'css-loader?modules&importLoaders=1&localIdentName=[name]'
                }),
            },
            {
                test: /\.scss$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: 'css-loader' +
                    '!sass-loader',
                }),
            },
            {
                test: /\.woff2?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                // Limiting the size of the woff fonts breaks font-awesome ONLY for the extract text plugin
                // use: "url?limit=10000"
                use: 'url-loader',
            },
            {
                test: /\.(png|jpg|gif|ttf|eot|svg)(\?[\s\S]+)?$/,
                use: {
                    loader: 'file-loader',
                    options: {
                        name: "[hash].[ext]",
                        outputPath: "assets/"
                    }
                },
            },
            {
                test: /\.vue$/,
                loader: "vue-loader",
                options: {
                    loaders: {
                    }
                    // other vue-loader options go here
                }
                // options: {
                //     loaders: {
                //         css: ExtractTextPlugin.extract({
                //             use: "css-loader",
                //             fallback: "vue-style-loader"
                //         })
                //     }
                // }
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["babel-preset-env"]
                    }
                }
            },
            {test: /bootstrap-sass\/assets\/javascripts\//, use: 'imports-loader?jQuery=jquery'},
            // {
            //     test: require.resolve("jquery"),
            //     use: [{
            //         loader: 'expose-loader',
            //         options: 'jQuery'
            //     },{
            //         loader: 'expose-loader',
            //         options: '$'
            //     }]
            // }
        ]
    },
    plugins: [
        new CleanWebpackPlugin(['web/build']),
        new ExtractTextPlugin({filename: "[name].css", allChunks: true}),
        new webpack.DefinePlugin({
            "process.env": {
                NODE_ENV: '"development"',
                // NODE_ENV: '"production"',
            }
        })
    ]
};