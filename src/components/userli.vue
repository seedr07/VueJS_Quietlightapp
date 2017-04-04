<template>
  <div class='userli twelve wide column'>
    <span class="index">{{index}}.</span>
    <p>
      <a class="title" href="{{href}}" >{{user | realName}}</a>
    </p>
    <p class="subtext">
        <created v-if="user.created_at" at="{{user.created_at}}"></created>
        <updated v-if="user.updated_at" at="{{user.updated_at}}"></updated>
    </p>
  </div>
</template>

<script>
module.exports = {
  replace: true,
  props: ['user', "offset"],
  computed: {
    href: function () {
      return ('#/user/' + this.user.id)
    },
    index: function () {
        return this.$index + 1 + offset;
    },
  },
  components: {
    created: require('../components/created.vue'),
    updated: require('../components/updated.vue'),
  },
}
</script>

<style lang="stylus">

.userli
  padding 2px 0 2px 40px !important
  position relative
  transition background-color .2s ease
  p
    margin 2px 0
  .title:visited
      color $gray
  .index
    color $gray
    position absolute
    width 30px
    text-align right
    left 0
    top 4px
  .subtext
    font-size 11px
    color $gray
    a
      color $gray
  .subtext a:hover
    text-decoration underline
</style>