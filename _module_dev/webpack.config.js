// webpack.config.js
const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');
const partytown = require('@builder.io/partytown/utils');

module.exports = {
    entry: {
        partytown: './src/index.js',
    },
    output: {
        path: path.resolve(__dirname, '../public'),
    },
    resolve: {
        extensions: ['.ts', '.js', '.mjs'],
    },
    plugins: [
        new CopyPlugin({
            patterns: [
                {
                    from: partytown.libDirPath(),
                    to: path.join(__dirname, '../public', '~partytown'),
                },
            ],
        }),
    ],
};
