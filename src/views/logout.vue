<template>
</template>

<script>
var store = require('../store')

module.exports = {
  replace: true,
  props: ['params'],
  data: function () {
    return {
    }
  },
  watch: {
  },
  attached: function() {
    this.doLogout();
  },
  compiled: function () {
  },
  destroyed: function () {
  },
  components: {
  },
  methods: {
    doLogout: function () {
      logout = this;
      store.logout(function(error, response){
        logout.$parent.router.setRoute("/login");
      });
    }
  }
}


</script>

<style lang="stylus">

</style>
