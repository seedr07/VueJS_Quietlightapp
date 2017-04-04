<template>
  <div class='sixteen wide column sortable' data-section-weight="{{section.weight}}" data-section-id="{{section.id}}">
    <addendum v-if="section.table == 'Addendum'" addendum="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></addendum>
    <disclosure v-if="section.table == 'Disclosure'" disclosure="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></disclosure>
    <executivesummary v-if="section.table == 'ExecutiveSummary'" executivesummary="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></executivesummary>
    <!--<financial v-if="section.table == 'Financial'" financial="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></financial>  -->
    <financialv2 v-if="section.table == 'Financialv2' || section.table == 'FinancialV2'" financial="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></financialv2>
    <flex v-if="section.table == 'Flex'" flex="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></flex>
    <question v-if="section.table == 'Question'" headers="{{headers}}" question="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></question>
    <questionbox v-if="section.table == 'Questionbox'" headers="{{headers}}" questionbox="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></questionbox>
    <title v-if="section.table == 'Title'" title="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></title>
    <webtraffic v-if="section.table == 'Webtraffic'" webtraffic="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></webtraffic>
    <webtraffic v-if="section.table == 'WebTraffic'" webtraffic="{{section.association}}" update-section-name="{{updateSectionName}}" section="{{section}}"></webtraffic>
  </div>
</template>

<script>
var store = require("../store");
module.exports = {
  data: {
    section: {}
  },
  replace: true,
  props: ['section', 'headers', 'updateSection'],
  attached: function(){
    $(this.$el).on("click", ".remove-section", this.removeSection);
  },
  detached: function(){
    this.section = null;
  },
  computed: {
  },
  components: {
    addendum: require('../models/addendum.vue'),
    disclosure: require('../models/disclosure.vue'),
    executivesummary: require('../models/executivesummary.vue'),
    financial: require('../models/financial.vue'),
    financialv2: require('../models/financialv2.vue'),
    flex: require('../models/flex.vue'),
    question: require('../models/question.vue'),
    questionbox: require('../models/questionbox.vue'),
    title: require('../models/title.vue'),
    webtraffic: require('../models/webtraffic.vue'),
  },
  methods: {
    removeSection: function(){
      var comp = this;
      store.destroySummarySection(this.section.id, function(){
        comp.section = null;
      });
    },
    updateSectionName: function(e){
      this.section.name = e.name;
      this.updateSection(e);
    }
  }
}
</script>

<style lang="stylus">

    

    .sortable.sortable-moving
      opacity: .4




.summary
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