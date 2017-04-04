/**
 * Include Semantic UI (in node_modules)
 */
require('semantic-ui-js');
require('semantic-ui-css');

/* HTML5 sortable */
require('sortable');

/* Redactor */
require('redactor-js');
require('redactor-css');
require('redactor-table');
require('redactor-images');


require('timeago');


/**
 * Boot up the Vue instance and wire up the router.
 */

var Vue = require('vue')
Vue.config.debug = true 
var app = new Vue(require('./app.vue'))
var Router = require('director').Router
var router = new Router()
var localstore = require('cookies-js') //require('store')

app.localstore = localstore
app.router = router;

//Load Google's chartting
// Load the Visualization API and the piechart package.
//google.load('visualization', '1.0', {'packages':['corechart']});


window.$ = $;

router.on('/', function () {
  app.view = 'dashboard'
  app.params.user = (localstore.get('user')) ? JSON.parse(localstore.get('user')) : false;
})

router.on('/admin', function () {
  app.view = 'admin'
  app.params.user = (localstore.get('user')) ? JSON.parse(localstore.get('user')) : false;
})

router.on('/user/:id', function (id) {
  app.view = 'user'
  app.params.user = (localstore.get('user')) ? JSON.parse(localstore.get('user')) : false;
  app.params.user_id = id;
})

router.on('/summary/create', function (id) {
  app.view = 'create'
  app.params.user = (localstore.get('user')) ? JSON.parse(localstore.get('user')) : false;
})

router.on('/summary/:id', function (id) {
  app.view = 'summary'
  app.params.user = (localstore.get('user')) ? JSON.parse(localstore.get('user')) : false;
  app.params.summary_id = id;
  app.params.summaries = [];
})

router.on('/login', function () {
  app.view = 'login'
})

router.on('/logout', function () {
  app.view = 'logout'
})


if(!localstore.get("user")){
  router.init('/login');
} else {
  router.init('/');
}
