const BabelLoaderExcludeNodeModulesExcept = require('babel-loader-exclude-node-modules-except');
const CssoWebpackPlugin = require('csso-webpack-plugin').default;
const DeadCodePlugin = require('webpack-deadcode-plugin');
const ESLintPlugin = require('eslint-webpack-plugin');
const fs = require('fs');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');
const webpack = require('webpack');
const webpackConfig = require('@nextcloud/webpack-vue-config');
const xml2js = require('xml2js');

const infoFile = path.join(__dirname, 'appinfo/info.xml');
let appInfo;
xml2js.parseString(fs.readFileSync(infoFile), function(err, result) {
  if (err) {
    throw err;
  }
  appInfo = result;
});
const appName = appInfo.info.id[0];
const productionMode = process.env.NODE_ENV === 'production';

webpackConfig.entry = {
  'admin-settings': path.join(__dirname, 'src', 'admin-settings.js'),
  'personal-settings': path.join(__dirname, 'src', 'personal-settings.js'),
  'files-hooks': path.join(__dirname, 'src', 'files-hooks.ts'),
  'files-sidebar-hooks': path.join(__dirname, 'src', 'files-sidebar-hooks.js'),
};

webpackConfig.output = {
  // path: path.resolve(__dirname, 'js'),
  path: path.resolve(__dirname, '.'),
  publicPath: '',
  filename: 'js/[name]-[contenthash].js',
  assetModuleFilename: 'js/assets/[name]-[hash][ext][query]',
  chunkFilename: 'js/chunks/[name]-[contenthash].js',
  clean: false,
  compareBeforeEmit: true, // true would break the Makefile
};

webpackConfig.plugins = webpackConfig.plugins.concat([
  new webpack.DefinePlugin({
    APP_NAME: JSON.stringify(appName),
  }),
  new ESLintPlugin({
    extensions: ['js', 'vue'],
    exclude: [
      'node_modules',
    ],
  }),
  new HtmlWebpackPlugin({
    inject: false,
    filename: 'js/asset-meta.json',
    minify: false,
    templateContent(arg) {
      return JSON.stringify(arg.htmlWebpackPlugin.files, null, 2);
    },
  }),
  new webpack.ProvidePlugin({
    // $: 'jquery',
  }),
  new MiniCssExtractPlugin({
    filename: 'css/[name]-[contenthash].css',
  }),
  new CssoWebpackPlugin(
    {
      pluginOutputPostfix: productionMode ? null : 'min',
    },
    productionMode ? /\.css$/ : /^$/
  ),
  new DeadCodePlugin({
    patterns: [
      'src/**/*.(js|jsx|css|vue)',
      'style/**/*.scss',
    ],
    exclude: [
      'src/toolkit/util/file-download.js',
      'src/toolkit/util/on-document-loaded.js',
      'src/toolkit/util/pangram.js',
    ],
  }),
]);

// webpackConfig.module.rules = webpackConfig.module.rules.concat([
webpackConfig.module.rules = [
  {
    test: /\.xml$/i,
    use: 'xml-loader',
  },
  {
    test: /\.css$/,
    use: [
      // 'style-loader',
      MiniCssExtractPlugin.loader,
      'css-loader',
    ],
  },
  {
    test: /\.s(a|c)ss$/,
    use: [
      // 'style-loader',
      MiniCssExtractPlugin.loader,
      'css-loader',
      {
        loader: 'sass-loader',
        options: {
          // Prefer `dart-sass`
          implementation: require('sass'),
          additionalData: '$appName: ' + appName + '; $cssPrefix: ' + appName + '-;',
        },
      },
    ],
  },
  {
    test: /\.(jpe?g|png|gif)$/i,
    type: 'asset', // 'asset/resource',
    generator: {
      filename: './css/img/[name]-[hash][ext]',
      publicPath: '../',
    },
  },
  {
    test: /\.svg$/i,
    loader: 'svgo-loader',
    type: 'asset', // 'asset/resource',
    generator: {
      filename: './css/img/[name]-[hash][ext]',
      publicPath: '../',
    },
    options: {
      multipass: true,
      js2svg: {
        indent: 2,
        pretty: true,
      },
      plugins: [
        {
          name: 'preset-default',
          params: {
            overrides: {
              // viewBox is required to resize SVGs with CSS.
              // @see https://github.com/svg/svgo/issues/1128
              removeViewBox: false,
            },
          },
        },
      ],
    },
  },
  {
    test: /\.vue$/,
    loader: 'vue-loader',
  },
  {
    test: /\.js$/,
    loader: 'babel-loader',
    // automatically detect necessary packages to
    // transpile in the node_modules folder
    exclude: BabelLoaderExcludeNodeModulesExcept([
      '@nextcloud/dialogs',
      '@nextcloud/event-bus',
      'davclient.js',
      'nextcloud-vue-collections',
      'p-finally',
      'p-limit',
      'p-locate',
      'p-queue',
      'p-timeout',
      'p-try',
      'semver',
      'striptags',
      'toastify-js',
      'v-tooltip',
      'yocto-queue',
    ]),
  },
  {
    test: /\.tsx?$/,
    use: [
      'babel-loader',
      {
        // Fix TypeScript syntax errors in Vue
        loader: 'ts-loader',
        options: {
          transpileOnly: true,
        },
      },
    ],
    exclude: BabelLoaderExcludeNodeModulesExcept([]),
  },
  {
    resourceQuery: /raw/,
    type: 'asset/source',
  },
  {
    test: /\.svg$/i,
    resourceQuery: /raw/,
    loader: 'svgo-loader',
    type: 'asset/source',
    options: {
      multipass: true,
      js2svg: {
        indent: 2,
        pretty: true,
      },
      plugins: [
        {
          name: 'preset-default',
          params: {
            overrides: {
              // viewBox is required to resize SVGs with CSS.
              // @see https://github.com/svg/svgo/issues/1128
              removeViewBox: false,
            },
          },
        },
      ],
    },
  },
];

webpackConfig.resolve.modules = [
  path.resolve(__dirname, 'style'),
  path.resolve(__dirname, 'src'),
  path.resolve(__dirname, 'img'),
  path.resolve(__dirname, '.'),
  'node_modules',
];

webpackConfig.stats = {
  errorDetails: true,
};

module.exports = webpackConfig;
