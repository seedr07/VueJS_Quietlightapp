<template>
  <div class="dashboard-view grid ui padded" v-class="loading:!summaries.length">
    <!-- item list -->
    <summary offset=0  v-repeat="summary:summaries" track-by="id"></summary>
    <!-- navigation 
    <div class="nav" v-show="items.length > 0">
      <a v-if="params.page > 1" href="#/news/{{params.page - 1}}">&lt; prev</a>
      <a v-if="params.page < 4" href="#/news/{{params.page + 1}}">more...</a>
    </div>-->
    <div v-if="summaries && !summaries.length">
      <h4>No summaries created yet!</h4>
    </div>
    <div class='sixteen wide column paging'>
      <a v-if="showPrev" class='page-prev'>&lt;&lt; prev</a>
      <a v-if="showNext" class='page-next'>next &gt;&gt;</a>
    </div>
  </div>

  </div>
</template>

<script>
var store = require('../store')

module.exports = {
  replace: true,
  props: ['params', 'showNext', "showPrev", "limit", "offset"],
  attached: function() {
    if(!this.$parent.localstore.get('user')){
      this.$parent.router.setRoute('/login')
    }
    this.update();
  },
  data: function () {
    return {
      summaries: false,
      showNext: false,
      showPrev: false,
      limit: 12,
      offset: 0
    }
  },
  ready: function(){
    $(".paging").on("click","a.page-prev", this.prevPage);
    $(".paging").on("click","a.page-next", this.nextPage);
  },
  created: function() {
    this.$watch('offset',this.update,{deep: true});
    this.$watch('limit',this.update,{deep: true});
  },
  compiled: function () {
    store.on('update', this.update);
  },
  destroyed: function () {
  },
  components: {
    summary: require('../components/summary.vue')
  },
  methods: {
    update: function () {
      comp = this;

      limit = (comp.limit) ? comp.limit : 10;
      offset = (comp.offset) ? comp.offset : 0;
      
      store.getSummarys(limit,offset,function (error, summaries) {
        comp.showNext = summaries.next;
        comp.showPrev = summaries.prev;
        comp.summaries = summaries.summaries;
      });
    },

    nextPage: function () {
      if(this.showNext) this.offset += this.limit;
    },

    prevPage: function () {
      if(this.showPrev) this.offset -= (this.offset >= this.limit) ? this.limit : this.offset;
    }
  }
}
</script>

<style lang="stylus">
  .page-prev
    float left
  .page-next
    float right
  .paging
    height 2em
</style>
