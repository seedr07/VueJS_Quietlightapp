<template>
  <div class='summary four wide column'>
    <div>
        <a v-if="summary.thumb" target="_blank" class='thumb' href="{{summary.file}}"><img  src="{{summary.thumb}}" /></a>
        <i v-if="!summary.thumb" class="fa fa-gear fa-4x"></i>
        <p class="name">
          <a class="title" href="{{href}}" >{{summary.name}}</a>
        </p>
        <p class="subtext">
            <created v-if="summary.created_at" by="{{summary.created_by}}" at="{{summary.created_at}}"></created><br />
            <updated v-if="summary.updated_at" by="{{summary.updated_by}}" at="{{summary.updated_at}}"></updated>
        </p>
      </a>
    </div>
  </div>
</template>

<script>
module.exports = {
  replace: true,
  props: ['summary', "offset"],
  computed: {
    href: function () {
      return ('#/summary/' + this.summary.id)
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

.summary 
  div
    padding 10px 0px 
    position relative
    transition background-color .2s ease
    text-align center
    min-height 285px

    a.thumb
      position relative

      &:after
            font-family: 'FontAwesome';
            position: absolute;
            opacity: 0;
            transition: all 0.5s;
            content: '\f019';
            width: 100%;
            height: 198px;
            top: -185px;
            left: 0;
            background: rgba(0,0,0,0.6);
            color: #FFF;
            font-size: 4em;
            padding-top: 1.75em;

      &:hover
        &:after
          opacity: 1;


    i, img  
      display inline-block
      height 198px

    i
      font-size 100px
      padding-top 45px

    p
      margin 2px 0
      text-decoration none
    .title:visited
        color #000
    .index
      color #000
      position absolute
      width 30px
      text-align right
      left 0
      top 4px

    .subtext
      font-size 11px
      color #000

    p.name
      font-size 14px

      a:hover
        text-decoration underline

    a
      color #000 
      &:hover
        color #000
</style>