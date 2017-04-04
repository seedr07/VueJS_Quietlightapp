<template>
  <div class='ui one column centered grid'>
    <div class="two column centered row">
      <div class="column">
        <form class="ui form">
          <div class="ui info message hide"></div>
          <h4 class="ui dividing header">New Summary Information <span v-if="status">(Creating: {{status}})</span></h4>
          <div class="one field">
            <div class="required field">
              <label>Name</label>
              <div class="field">
                <input type="text" name="name" placeholder="Summary Name">
              </div>
            </div>
          </div>

          <h2 class="ui dividing header"></h2>
          
          <div class="ui submit button">Create Summary</div>

          <div class="ui error message">
            <div class="header">We noticed some issues</div>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script>
var store = require('../store')

var rules = {
  name: {
    identifier: 'name',
    rules: [{ type: 'empty', prompt: 'A summary name is required' }]
  }
};

var settings = {
  onSuccess : function(ev) {
    store.emit('createSummary');
    ev.preventDefault();
    return false;
  }
};

module.exports = {
  replace: true,
  props: ["status", "summary_id"],
  ready: function() {
    this.uiForm = $('.ui.form').form(rules, settings);
  },
  compiled: function() {
    store.on('createSummary', this.createSummary);
  },
  destroyed: function () {
    store.removeListener('createSummary', this.createSummary);
  },
  attached: function() {
  },
  watch: {
  },
  components: {
  },
  methods: {

    createSummary: function() {
      var comp = this;
      var name = $(this.$el).find('[name=name]').val();
      comp.status = "Summary";

      var createdTitle = function(error, result){
        comp.status = "Disclosures";
        store.createSummarySection(comp.summary_id, "Title", "Title", result.id, null);
        store.createDisclosure(comp.summary_id, null, createdDisclosures);
      };
      var createdDisclosures = function(error, result){
        comp.status = "Executive Summary";
        store.createSummarySection(comp.summary_id, "Disclosure", "Disclosure", result.id, null);
        store.createExecutiveSummary(comp.summary_id, null, null, null, null, null, null, null, null, null, null, createdExecutiveSummary);
      };
      var createdExecutiveSummary = function(error, result){
        comp.status = "Financial";
        store.createSummarySection(comp.summary_id, "ExecutiveSummary", "Executive Summary", result.id, null);
        store.createFinancialV2(comp.summary_id, null, null, null, null, null, null, null, null, createdFinancial);
      };
      var createdFinancial = function(error, result){
        debugger;
        comp.status = "WebTraffic";
        store.createSummarySection(comp.summary_id, "FinancialV2", "Financial", result.id, null);
        store.createWebTraffic(comp.summary_id, null,finishedCreating);
      };
      var finishedCreating = function(error, result){
        comp.status = "Finished";
        store.createSummarySection(comp.summary_id, "WebTraffic", "WebTraffic", result.id, null, function(){
          comp.$parent.router.setRoute("/summary/"+comp.summary_id);
        });
        
      };

      var createAssets = function(error, result){
        comp.status = "Title";
        comp.summary_id = result.id;
        store.createTitle(comp.summary_id, result.name, null, null, JSON.parse(comp.$parent.localstore.get("user")).id, createdTitle);
      }

      store.createSummary(name, createAssets);
    }
 
  }
}
</script>

<style lang="stylus">
</style>
