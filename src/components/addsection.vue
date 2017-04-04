<template>
  <div class="ui modal">
    <i class="close icon"></i>
    <div class="header">
      Add Section
    </div>
    <div class="content">
      <div class='ui one column centered grid'>
          <div class='column'>
            Select the type of section to add.
          </div>
          <div class='column sections'>
            <div data-model='addendum' class='ui button primary'>Addendum</div>
            <div data-model='flex' class='ui button primary'>Flex</div>
            <div data-model='financialv2' class='ui button primary'>Financial</div>
            <div data-model='questionbox' class='ui button primary'>Question/Answer</div>
            <div data-model='webtraffic' class='ui button primary'>WebTraffic</div>
          </div>
      </div>
    </div>
  </div>
</template>

<script>
var store = require("../store");

module.exports = {
  replace: true,
  props: ['summary_id'],
  attached: function(){
    $('.sections .button').click(this.addSection);
  },
  computed: {
  },
  components: {
  },
  methods: {
    addSection: function(ev){
      var button = ev.currentTarget;
      var type = $(button).data('model');
      var comp = this;

      $(button).addClass("loading");

      var addSection = function(error, result){
        if("id" in result){
          var title = "";
          switch(type.toLowerCase()){
            case "executivesummary":
              title = "Executive Summary";
              break;
            case "webtraffic":
              title = "Web Traffic";
              break;
            case "questionbox":
              title = "Client Interview";
              break;
            case "flex":
              title = "Flex";
              break;
            case "addendum":
              title = "Addendum";
              break;
            case "financial":
            case "financialv2":
              title = "Financial";
              break;
            default:
              title = type;
              break; 
          }

          store.createSummarySection(comp.summary_id, type, title, result.id, null);
        } else {
          alert('Failed to add section.');
        }

        $(button).removeClass("loading");
        $(".ui.modal").modal('hide');

      };

      switch(type){
        case "addendum":
          store.createAddendum(this.summary_id, null, null, addSection);
          break;
        case "questionbox":
          store.createQuestionBox(this.summary_id, null, addSection);
          break;
        case "flex":
          store.createFlex(this.summary_id, null, 'P', addSection);
          break;
        case "financialv2":
          store.createFinancialV2(this.summary_id, null, null, null, null, null, null, null, null, addSection);
          break;
        case "webtraffic":
          store.createWebTraffic(this.summary_id, null, addSection);
          break;
      }
    }
  }
}
</script>

<style lang="stylus">
</style>