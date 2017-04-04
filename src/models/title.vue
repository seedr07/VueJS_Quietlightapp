<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form title">
          <input type='hidden' name='title.id' lazy v-model="title.id" />
          <div class="ui info message hide"></div>
          <h2 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /></h2>
          <div class="one field">
            <div class="required field">
              <label>Display Name</label>
              <div class="field">
                <input type="text" name="title.name" lazy v-model="title.name">
              </div>
            </div>
          </div>
          <!--
          <div class="one field">
            <div class="required field">
              <label>Tagline</label>
              <div class="field">
                <input type="text" name="title.tagline" lazy v-model="title.tagline">
              </div>
            </div>
          </div>
          -->
          <div class="two fields">
            <div class="required field">
              <label>Asking Price</label>
              <div class="field">
                <input type="text" name="title.asking_price" lazy v-model="title.asking_price | money">
              </div>
            </div>
            <div class="required field">
              <label>Advisor</label>
              <div class="field">
                <advisors name='title.advisor_id' select="{{title.advisor_id}}"></advisors>
              </div>
            </div>
          </div>
          <div class='text-right'>
            <small v-if="updated">Last updated <timeago time="{{updated}}"></timeago> by {{updated_by | realName}}</small>
          </div>
          <div class="ui error message">
            <div class="header">We noticed some issues</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var store = require('../store')

var formSettings = {
  on: 'blur',
  fields: {
    email: {
      identifier: 'title.name',
      rules: [{ type: 'empty', prompt: 'Please enter the summary name.' }]
    },
    advisor_id: {
      identifier: 'title.advisor_id',
      rules: [{ type: 'empty', prompt: 'Please select an advisor.' }]
    },
    asking_price: {
      identifier: 'title.asking_price',
      rules: [{ type: 'empty', prompt: 'Please enter an asking price.' }]
    }
  }
};

module.exports = {
  replace: true,
  props: ['last_update','updateSectionName', 'section', 'title'],
  ready: function() {
    this.uiForm = $('.ui.form', this.$el).form(formSettings);
    $(".ui.checkbox").checkbox();
  },
  created: function() {
    this.$watch('title',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.title.updated_at;
    },
    updated_by: function() {
      return (this.title.updated_by) ? this.title.updated_by : JSON.parse(this.$root.localstore.get("user"));
    }
  },
  attached: function() {
  },
  compiled: function () {
  },
  detached: function () {
  },
  destroyed: function () {
  },
  components: {
    advisors: require("../components/advisors.vue")
  },
  methods: {
    update: function(){
      if(this.$parent.$parent.fetchingSections) return;

      //format asking price
      asking_price = this.title.asking_price;

      store.updateTitle(this.title.id, 
                        this.title.name, 
                        this.title.tagline,
                        asking_price,
                        this.title.advisor_id);

      this.last_update = new Date().getTime()/1000;
    },
    advisorUpdate: function(advisor_id){
      this.title.advisor_id = advisor_id;
    }
  }
}


</script>

<style lang="stylus">
  h2 
    margin-top 0px !important

  .sectionname 
    margin 0px !important
    padding 0px !important
    border none !important
    background transparent !important
    &:hover 
      text-decoration underline
</style>
