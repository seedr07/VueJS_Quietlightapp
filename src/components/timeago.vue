<template>
  <abbr title="{{iso}}">{{iso}}</abbr>
</template>

<script>
module.exports = {
  replace: true,
  props: ['time'],
  computed: {
    iso: function () {
      var d = new Date(this.time*1000);
      return (d) ? d.toISOString() : false;
    },
  },
  attached: function(){
  	$(this.$el).timeago('dispose');
  	$(this.$el).timeago();
    this.$watch('time',this.reinit);
  },
  methods: {
    reinit: function(){
      $(this.$el).timeago('update', this.iso);
    }
  }
}
</script>

<style lang="stylus">
</style>
