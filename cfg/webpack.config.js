const path = require('path');
const HTMLPlugin = require('html-webpack-plugin');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin'); // 抽取css文件
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin'); // 压缩已抽取的css文件
const CleanWebpackPlugin = require('clean-webpack-plugin');


const isDev = process.env.NODE_ENV === 'development';

function resolve(dir) {
  return path.join(__dirname, dir);
}

const config = {
  target: 'web',
  entry: path.join(__dirname, 'src/admin/main.js'),
  output: {
    filename: 'bundle.js',
    path: path.join(__dirname, 'dist')
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader:"babel-loader"
        }
      },
      {
        test: /\.(gif|jpg|jpeg|png|svg)$/,
        use: [
          {
            loader: 'url-loader',
            options: {
              limit: 1024,
              name: 'images/[name]-[hash:8].[ext]'
            }
          }
        ]
      },
      {
        test: /\.(eot|woff|ttf|svg)$/,
        use: [
          {
            loader: 'url-loader',
            options: {
              limit: 100000
            }
          }
          // {
          //   loader: 'file-loader',
          //   options: {
          //     name: '[name]-[hash:8].[ext]'
          //   }
          // }
        ]
      }
    ]
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: isDev ? '"development"' : '"production"'
      }
    }),
    new HTMLPlugin({
      template: './index.html',
    })
  ],
  resolve: {
    extensions: ['.js', '.vue', '.json', '.scss', '.css'],
    alias: {
      'admin': resolve('src/admin'),
      'admin/assets': resolve('src/admin/assets'),
      'admin/components': resolve('src/admin/components'),
      'admin/router': resolve('src/admin/router'),
      'admin/api': resolve('src/admin/api'),
      'admin/store': resolve('src/admin/store'),
      'admin/views': resolve('src/admin/views')
    }
  }
}

if (isDev) {
  config.output.path = path.join(__dirname, 'dist/dev');
  config.module.rules.push(
    {
      test: /\.css$/,
      use: [
        'style-loader', // 开发环境的样式最后渲染在index页面上
        'css-loader'
      ]
    },
    {
      test: /\.(scss|sass)$/,
      use: [
        'style-loader', // 开发环境的样式最后渲染在index页面上
        'css-loader',
        {
          loader: 'postcss-loader',
          options: {
            sourceMap: true
          }
        },
        'sass-loader'
      ]
    }
  );
  config.devtool = '#cheap-module-eval-source-map';
  config.devServer = {
    port: 9999,
    host: '0.0.0.0',
    overlay: {
      errors: true,
    },
    hot: true
  };
  config.plugins.push(
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NoEmitOnErrorsPlugin(),
    new CleanWebpackPlugin('dist/dev/*.*', {
      root: __dirname,
      verbose: true,
      dry: false
    })
  );
} else {
  config.entry = {
    app: path.join(__dirname, 'src/admin/main.js'),
    // vendor: ['vue'] // 这里webpack4被optimization替换了
  };
  config.output.filename = '[name].[chunkhash:8].js';
  config.module.rules.push(
    {
      test: /\.css$/,
      use: [
        MiniCssExtractPlugin.loader,
        'css-loader' // 生产环境的样式最后打包为css文件
      ]
    },
    {
      test: /\.scss$/,
      use: [
        MiniCssExtractPlugin.loader,
        'css-loader', // 生产环境的样式最后打包为css文件
        {
          loader: 'postcss-loader',
          options: {
            sourceMap: true
          }
        },
        'sass-loader'
      ]
    }
  );
  config.plugins.push(
    new MiniCssExtractPlugin({
      filename: '[name].[contenthash:8].css'
    }),
    new OptimizeCSSAssetsPlugin({}),
    new CleanWebpackPlugin('dist/*.*', {
      root: __dirname,
      verbose: true,
      dry: false
    })
  );
  config.optimization = {
    splitChunks: {
      cacheGroups: {
        commons: {
          chunks: 'initial',
          minChunks: 2, maxInitialRequests: 5,
          minSize: 0
        },
        vendor: {
          test: /node_modules/,
          chunks: 'initial',
          name: 'vendor',
          priority: 10,
          enforce: true
        }
      }
    },
    runtimeChunk: true
  }
}

module.exports = config;