<template>
  Updated <timeago time="{{at}}"></timeago> <span v-if="by">by <a v-if="href" href="{{href}}">{{by | realName}}</span></a>.
</template>

<script>
module.exports = {
  replace: true,
  props: ['by','at'],
  computed: {
    href: function () {
      return (this.by.id) ? ('#/user/' + this.by.id) : "#";
    },
  }
}
</script>

<style lang="stylus">
</style>
