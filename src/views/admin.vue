<template>
  <div class="admin-view grid ui padded" v-class="loading:!users.length">
    <!-- user list -->
    <h4 class='ui dividing header'>Users <a href='#/user/add' class='user-add'>add user</a></h4>
    <userli offset=0 v-repeat="user:users" track-by="id"></userli>
    <div class='sixteen wide column paging'>
      <a v-if="userprev" class='page-userprev'>&lt;&lt; prev</a>
      <a v-if="usernext" class='page-usernext'>next &gt;&gt;</a>
    </div>

  </div>

  </div>
</template>

<script>
var store = require('../store')

module.exports = {
  replace: true,
  props: ['params', 'usernext', "userprev", "userlimit", "useroffset", "user", "users"],
  attached: function() {
    if(!this.$parent.localstore.get('user')){
      this.$parent.router.setRoute('/login');
    }
    if(!JSON.parse(this.$parent.localstore.get('user')).is_admin) {
        this.$parent.router.setRoute("/");
    }

    this.updateUsers();
  },
  created: function() {
    this.$watch('useroffset',this.updateUsers,{deep: true});
    this.$watch('userlimit',this.updateUsers,{deep: true});
  },
  data: function () {
    return {
      users: false,
      userlimit: 10,
      useroffset: 0
    }
  },
  ready: function(){
    $(".paging").on("click","a.page-userprev", this.prevUserPage);
    $(".paging").on("click","a.page-usernext", this.nextUserPage);
  },
  compiled: function () {
  },
  destroyed: function () {
  },
  components: {
    userli: require('../components/userli.vue')
  },
  methods: {
    updateUsers: function () {
      comp = this;

      limit = (comp.userlimit) ? comp.userlimit : 1;
      offset = (comp.useroffset) ? comp.useroffset : 0;
      
      store.user.list(limit,offset,function (error, result) {
        comp.usernext = result.next;
        comp.userprev = result.prev;
        comp.users = result.users;
      });
    },

    nextUserPage: function () {
      if(this.usernext) this.useroffset = this.useroffset + this.userlimit;
    },

    prevUserPage: function () {
      if(this.userprev) this.useroffset -= (this.useroffset >= this.userlimit) ? this.userlimit : this.useroffset;
    }
  }
}
</script>

<style lang="stylus">
  h4.header
    display block
    width 100%
  .page-userprev
    float left
  .page-usernext
    float right
  .paging
    height 2em
  .user-add 
    float right
    font-weight normal
</style>
