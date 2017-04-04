<template>
  <div class='ui one column centered grid executivesummary'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h2 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /></h2>
          
          <div class="one field required">
            <label>Summary</label>
            <div class='unedited'>{{{executivesummary.summary}}}</div>
            <textarea disabled lazy v-model="executivesummary.summary" name="executivesummary.summary" id="executivesummary_summary_editor" rows="10" cols="80"></textarea>
            <div class='ui button enable_editor'>Edit</div>
          </div>
          <div class="one field required">
            <label>Summary of Benefits</label>
            <div class='unedited'>{{{executivesummary.benefits}}}</div>
            <textarea disabled lazy v-model="executivesummary.benefits" name="executivesummary.benefits" id="executivesummary_benefits_editor" rows="10" cols="80"></textarea>
            <div class='ui button enable_editor'>Edit</div>
          </div>

          <h4 class="ui dividing header">Financial Quickview</h4>

          <div class="two fields">
            <div class="one field required">
              <label>Total Revenue</label>
              <input type='text' lazy v-model="executivesummary.total_revenue | money" name="executivesummary.total_revenue"></input>
            </div>
            <div class="one field required">
              <label>Discretionary Earnings</label>
              <input type='text' lazy v-model="executivesummary.discretionary_earnings | money" name="executivesummary.discretionary_earnings"></input>
            </div>
          </div>
          <div class="two fields">
            <div class="one field">
              <label>Gross Profit</label>
              <input type='text' lazy v-model="executivesummary.gross_profit | money" name="executivesummary.gross_profit"></input>
            </div>
            <div class="one field required" style="display: none;">
              <label>Asking Price</label>
              <input type='text' lazy v-model="executivesummary.asking_price | money" name="executivesummary.asking_price"></input>
            </div>
          </div>
          <div class="two fields">
            <div class="one field">
              <label>Inventory</label>
              <input type='text' lazy v-model="executivesummary.inventory | money" name="executivesummary.inventory"></input>
            </div>
            <div class="one field">
              <div class='two fields'>
                <div class='one field'>
                  <label class='mb1'>Inventory Included</label>
                  <div class="ui toggle checkbox">
                    <input type="checkbox" name="executivesummary.inventory_included" v-model="executivesummary.inventory_included | toBool">
                    <label>Included</label>
                  </div>
                </div>
                <div class="one field">
                  <label class='mb1'>Show Multiple</label>
                  <div class="ui toggle checkbox">
                    <input type="checkbox" name="executivesummary.show_multiple" v-model="executivesummary.show_multiple | toBool">
                    <label>Show Multiple</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="one field">
            <label>Inventory Footnote</label>
            <input type='text' lazy v-model="executivesummary.inventory_footnote" name="executivesummary.inventory_footnote"></input>
          </div>
          <div class='text-right'>
            <small v-if="updated">Last updated <timeago time="{{updated}}"></timeago>  by {{updated_by | realName}}</small>
          </div>
          <div class="ui error message">
            <div class="header">We noticed some issues</div>
          </div>
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
    revenue: {
      identifier: 'executivesummary.total_revenue',
      rules: [{ type: 'empty', prompt: 'Please enter total revenue.' }]
    },
    earnings: {
      identifier: 'executivesummary.discretionary_earnings',
      rules: [{ type: 'empty', prompt: 'Please enter discretionary earnings.' }]
    },
    asking_price: {
      identifier: 'executivesummary.asking_price',
      rules: [{ type: 'empty', prompt: 'Please enter an asking price.' }]
    }
  }
};


module.exports = {
  replace: true,
  props: ['last_update', 'updateSectionName', 'section', 'executivesummary', 'updated'],
  watch: {
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.executivesummary.updated_at;
    },
    updated_by: function() {
      return (this.executivesummary.updated_by) ? this.executivesummary.updated_by : JSON.parse(this.$root.localstore.get("user"));
    }
  },
  ready: function() {
    this.uiForm = $('.executivesummary .ui.form').form(formSettings);
    $(".executivesummary .ui.checkbox").checkbox();
    $('.executivesummary .ui.checkbox').checkbox('setting', 'onChecked', this.updateSelection);
    $('.executivesummary .ui.checkbox').checkbox('setting', 'onUnchecked', this.updateSelection);
    $(".executivesummary .enable_editor").click(this.enableEditor);
  },
  created: function() {
    this.$watch('executivesummary',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
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
  },
  methods: {
    editorUpdated: function(id, content){
      var field = id.replace("executivesummary_","").replace("_editor","");

      //replace Office's funny quotes with real ones.
      content = content.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"');
      
      this.executivesummary[field] = content;
    },
    enableEditor: function(ev){
      var comp = this;
      var $edit = $(ev.currentTarget).parent().find("textarea");
      var id = $(ev.currentTarget).attr('id');
      $edit.prop('disabled',false);
      $(ev.currentTarget).parent().find('.unedited').remove();
      $(ev.currentTarget).remove();
      $edit.redactor({
          focus: true,
          plugins: ['table','imagemanager'],
          imageUpload: '/scripts/upload.php',
          imageManagerJson: '/scripts/imagelist.php',
          changeCallback: function(content){
            comp.editorUpdated($(this.$editor).parent().find("textarea").attr('id'), content);
          }
      });
    },
    update: function(){
      console.log("updated");
      if(this.$parent.$parent.fetchingSections) return;
      total_revenue = (typeof this.executivesummary.total_revenue == "string") ? parseFloat(this.executivesummary.total_revenue.replace(/[\$|,]/g,"")) : this.executivesummary.total_revenue;
      discretionary_earnings = (typeof this.executivesummary.discretionary_earnings == "string") ? parseFloat(this.executivesummary.discretionary_earnings.replace(/[\$|,]/g,"")) : this.executivesummary.discretionary_earnings;
      gross_profit = (typeof this.executivesummary.gross_profit == "string") ? parseFloat(this.executivesummary.gross_profit.replace(/[\$|,]/g,"")) : this.executivesummary.gross_profit;
      asking_price = this.executivesummary.asking_price;
      inventory = this.executivesummary.inventory;

      var comp = this;

      if(comp.saveTimer) clearTimeout(comp.saveTimer);

      comp.saveTimer = setTimeout(function(){
        store.updateExecutiveSummary(comp.executivesummary.id, 
                                   comp.executivesummary.summary, 
                                   comp.executivesummary.benefits, 
                                   total_revenue, 
                                   discretionary_earnings, 
                                   gross_profit, 
                                   asking_price, 
                                   inventory,
                                   comp.executivesummary.inventory_footnote,
                                   comp.executivesummary.inventory_included, 
                                   comp.executivesummary.show_multiple);
      }, 400);

      comp.last_update = new Date().getTime()/1000;
      
    },
    updateSelection: function(){
      comp = this;
      $(".executivesummary .ui.checkbox").each(function(){
        var name = $(this).find("input").attr('name').replace("executivesummary.","");
        var checked = ($(this).find("input").is(":checked")) ? 1 : 0;
        if(comp.executivesummary[name] != checked) comp.executivesummary[name] = checked;
      });
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
