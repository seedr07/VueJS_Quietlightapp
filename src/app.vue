<template>
  <div id="wrapper">
    <!-- header -->
    <div id="header">
      <h1><a href="#">QuietLight Brokerage Summarys</a></h1>
      <div class='login' v-if="!params.user">
        <a href='#/login'>Login</a>
      </div>
      <div class='logout' v-if="params.user">
        <a href='#/logout'>Logout</a>
      </div>
      <div class='user' v-if='params.user'>
        <a href='#/user/{{params.user.id}}'>{{params.user | realName}}</a> &nbsp; | &nbsp;
      </div>
      <div class='admin' v-if='params.user && params.user.is_admin'>
        <a href='#/admin'>Admin</a> &nbsp; | &nbsp;
      </div>
      <div class='create' v-if='params.user'>
        <a href='#/summary/create'>New Summary</a> &nbsp; | &nbsp;
      </div>
    </div>
    <div id="main">
      <!-- main view -->
      <component is="{{view}}"
        class="view"
        params="{{params}}"
        keep-alive
        v-transition
        transition-mode="out-in">
      </component>
    </div>
  </div>
</template>

<script>
var store = require('./store')

module.exports = {
  el: '#app',
  data: {
    view: '',
    fetchingSections: false,
    params: {
      user: null,
    }
  },
  attached: function() {
  },
  compiled: function() {
      store.on('logout', this.logout)
  },
  filters: {
    parseInt: require('./filters/parseInt'),
    fromNow: require('./filters/from-now'),
    realName: require('./filters/real-name'),
    money: require('./filters/money'),
    toBool: require('./filters/toBool')
  },
  components: {
    'dashboard': require('./views/dashboard.vue'),
    'summary': require('./views/summary.vue'),
    'admin': require('./views/admin.vue'),
    'create': require('./views/create.vue'),
    'user': require('./views/user.vue'),
    'login': require('./views/login.vue'),
    'logout': require('./views/logout.vue'),
    'timeago': require('./components/timeago.vue')
  },
  methods: {
    logout: function(){
      this.params.user = false;
    }
  }
}
</script>

<style lang="stylus">
@import "./shared.styl"

html, body
  font-family Verdana
  font-size 13px
  height 100%

ul
  list-style-type none
  padding 0
  margin 0

a
  color #000
  cursor pointer
  text-decoration none
  
#wrapper
  background-color $bg
  position relative
  width 85%
  min-height 100%
  margin 0 auto

#header
  background-color $green
  color #fff
  height 24px
  position relative

  .user, .admin, .create
    float right
    position relative
    top 3px
  
    &:hover 
      a
        text-decoration underline

  .login, .logout
    float right
    position relative
    top 3px
    padding-right 10px
    &:hover 
      a 
        text-decoration: underline
  
  h1
    font-size 13px
    display inline-block
    vertical-align middle
    margin 0 0 0 10px
  
  a
    color #fff
  
  .source
    color #fff
    font-size 11px
    position absolute
    top 4px
    right 4px
    a
      color #fff
      &:hover
        text-decoration underline

#main 
  padding 24px 24px 48px 24px
  height 100%

.redactor-editor ul, .unedited ul {
  list-style-type: square !important;
  list-style-position: inside !important;
}


@media screen and (max-width: 700px)
  html, body
    margin 0
  #wrapper
    width 100%
</style>
