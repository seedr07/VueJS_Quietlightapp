var webpack = require('webpack');

var bower_dir = __dirname + '/bower_components';
var node_dir = __dirname + '/node_modules';
var lib_dir = __dirname + '/public/js/libs';



var definePlugin = new webpack.DefinePlugin({
  __APIURL__: JSON.stringify(process.env.APIURL || 'http://localhost')
});

var providePlugin = new webpack.ProvidePlugin({
    $: "jquery",
    jQuery: "jquery",
    "window.jQuery": "jquery",
    "root.jQuery": "jquery"
});

config = {
  addVendor: function (name, path) {
      this.resolve.alias[name] = path;
  },
  entry: "./src/main.js",
  output: {
    path: './build',
    filename: "build.js"
  },
  resolve: {
    modulesDirectories: ['node_modules'],
    alias: {
      jquery: "jquery/src/jquery"
    }
  },
  module: {
    loaders: [
      { test: /\.vue$/, loader: "vue-loader" },
      { test: /\.css$/, loader: "style-loader!css-loader" },
      { test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "url-loader?limit=10000&minetype=application/font-woff" },
      { test: /\.(ttf|eot|svg|png|jpg|gif)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: "file-loader" },
      { test: /\.json$/, loader: 'json-loader' },
      { test: /[\/\\]node_modules[\/\\]some-module[\/\\]index\.js$/,  loader: "imports?this=>window" },
      { test: /[\/\\]node_modules[\/\\]some-module[\/\\]index\.js$/,  loader: "imports?define=>false" },
      { test: /jquery\/src\/selector\.js$/, loader: 'amd-define-factory-patcher-loader' }
    ]
  },
  plugins: [
    definePlugin,
    providePlugin
  ],
  node: {
    console: true,
    fs: 'empty',
    net: 'empty',
    tls: 'empty'
  }
}

config.addVendor('jquery', node_dir + '/jquery/dist/jquery.js');
config.addVendor('redactor-js', __dirname+"/redactor/redactor.js");
config.addVendor('redactor-css', __dirname+"/redactor/redactor.css");
config.addVendor('redactor-table', __dirname+"/redactor/plugins/table.js");
config.addVendor('redactor-images', __dirname+"/redactor/plugins/imagemanager.js");
config.addVendor('porter', bower_dir + '/Porter/lib/Porter.js');
config.addVendor('timeago', bower_dir + '/jquery-timeago/jquery.timeago.js');
config.addVendor('sortable', node_dir + '/html5sortable/dist/html.sortable.min.js');
config.addVendor('semantic-ui-js', node_dir + '/semantic-ui/dist/semantic.js');
config.addVendor('semantic-ui-css', node_dir + '/semantic-ui/dist/semantic.min.css');

module.exports = config;

