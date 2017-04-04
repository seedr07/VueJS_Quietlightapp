<template>
  <div class='text-right'>
    <a class='generate'>Generate PDF</a> | <a class='rename'>Rename</a> | <a class='delete'>Delete</a> 
  </div>
  <div>
    <h1 class='ui dividing header' v-if="summary">{{summary.name}} <a v-if="summary.file && !generating" class='ui button download primary' target="_blank" href="{{summary.file}}">Download PDF</a></h1>
    <small>
    <created v-if="summary && summary.created_by" by="{{summary.created_by}}" at="{{summary.created_at}}"></created>
    <updated v-if="summary && summary.updated_by" by="{{summary.updated_by}}" at="{{summary.updated_at}}"></updated>
    <generated v-if="summary && summary.generated_by && !generating" by="{{summary.generated_by}}" at="{{summary.generated_at}}"></generated>
    </small>
  </div>  
  <div v-if="generating" class="generating ui icon message">
    <i class="notched circle loading icon"></i>
    <div class="content">
      <div class="header">
        Just one second
      </div>
      <p>The PDF is generating.</p>
    </div>
  </div>
  <div v-if="generation_error" class="ui icon message error">
    <i class="circle error icon"></i>
    <div class="content">
      <div class="header">
        Uh Oh
      </div>
      <p>{{generation_error}}</p>
    </div>
  </div>
  <div class="ui warning message" v-if="logs && logs.length">
    <div class="header">
      Notice!
    </div>
    <ul>
    <li v-repeat="log:logs">{{{log}}}</li>
    </ul>
  </div>
  <div class="ui grid">
    <div class="four wide column">
      <br />
    <center><small>(Click and drag to reorder Summary Sections)</small></center>
      <ol>
        <li v-repeat="section:summary_sections"><span v-on="click: loadSection(section.id)" data-section-id={{section.id}}>{{section.name}}</span></li>
      </ol>
      <div class='add-section'><hr><div class='ui button secondary'>Add Section</div></div>
      <addsection summary_id="{{params.summary_id}}"></addsection>
    </div>
    <div class="twelve wide column">
      <div class="section-view grid ui padded" >
        <section update-section="{{updateSection}}" headers="{{headers}}" section="{{section}}"></section>
      </div>
    </div>
  </div>
  <renamemodal></renamemodal>
</template>

<script>
var store = require('../store')

module.exports = {
  replace: true,
  props: ['params', 'logs', 'summary', 'generation_error', 'generating', 'download', 'summary_sections', 'footnotes', "section"],
  detached: function() {
    this.summary_sections = [];
    this.summary = false;
    this.params.summary_id = false;
  },
  ready: function() {
    $('.add-section .button').click(this.addSection);
    $('.generate').unbind('click').click(this.generatePDF);
    $('.delete').unbind('click').click(this.confirmDelete);
    $('.rename').unbind('click').click(this.renameSummary);
  },
  attached: function() {
    this.generating = false;
    this.generation_error = false;

    this.getSummary();

    this.getFootnotes();

    store.on('summaryUpdated', this.summaryUpdateTime);

  },
  data: function () {
  },
  watch: {
    'params.summary_id': 'getSummary'
  },
  compiled: function () {
    store.on('createSection', this.getSections);
    store.on('destroySection', this.getSections);
  },
  destroyed: function () {
    store.removeListener('createSection', this.getSections)
    store.removeListener('destroySection', this.getSections)
  },
  components: {
    section: require("../components/section.vue"),
    footnote: require("../models/footnote.vue"),
    addsection: require("../components/addsection.vue"),
    created: require("../components/created.vue"),
    updated: require("../components/updated.vue"),
    generated: require("../components/generated.vue"),
  },
  methods: {
    getFootnotes: function(){
      var summary_id = this.params.summary_id;
      comp = this;
      store.getFootnotes(10000, 0, summary_id, function(error, results){
        comp.footnotes = results;
      });
    },
    summaryUpdateTime: function(){
      var comp = this;

      comp.summary.updated_at = new Date().getTime()/1000;
      
      //do this once a minute at most.
      if(!comp.lastUpdate || (comp.summary.updated_at-comp.lastUpdate > 60)){
        comp.lastUpdate = new Date().getTime()/1000;
        store.updateSummary(this.summary.id, null);
      }
    },
    getSummary: function(){
      var summary_id = this.params.summary_id;
      var comp = this;
      store.getSummary(summary_id, function(error, results){

        comp.summary = results;

        comp.getSections();
      });
    },
    loadSection: function(id){
      var comp = this;

      store.getSummarySection(id, function(error, result){
        comp.section = result;
      });

    },
    getSections: function(){
      var summary_id = this.params.summary_id;
      sum_comp = this;
      sum_comp.fetchingSections = true;

      var endFetch = function(){
        sum_comp.fetchingSections = false;
      }

      store.getSummarySections(1000, 0, summary_id, function(error, results){
        sum_comp.summary_sections = results.sections;
        sum_comp.headers = results.question_headers;
        setTimeout(sum_comp.initSorting, 500);
        setTimeout(endFetch, 1000);
      });
    },
    updateSection: function(e){
      var s = this.summary_sections.filter(function(t){ if(t.id == e.id) return t;})
      s[0].name = e.name;
      store.updateSummarySection(s[0].id, null, s[0].name, null, null);
    },
    addSection: function(){
      $(".ui.modal").modal('show');
    },
    initSorting: function(){
      //destroy old listeners
      if(this.sortable) $("ol").sortable('destroy');

      this.sortable = $("ol").sortable({
      }).bind('sortstop', this.handleDrop);
    },
    handleDrop: function(){
      var weight = 0;
      $("ol li").each(function(){
        weight++;
        if($(this).attr('data-section-weight') != weight){
          $(this).attr('data-section-weight', weight);
          store.updateSummarySection($(this).find("span").data('section-id'), null, null, null, weight);
        }
      });
    },
    confirmDelete: function(){
      if(!this.summary) return;
      comp = this;
      var confirmed = prompt("Type DELETE to confirm that you want to completely delete this summary and all it's sections.");
      if(confirmed.toLowerCase() == "delete"){
          store.destroySummary(this.params.summary_id, function(error, result){
            comp.$parent.router.setRoute("/");
          });
      }
    },
    renameSummary: function(){
      if(!this.summary) return;
      comp = this;
      var newname = prompt('Enter new Summary name.');
      if(newname){
        store.updateSummary(this.params.summary_id, newname, function(error, result){
          comp.summary = result;
        });
      }
    },
    generatePDF: function(){
      if(!this.summary) return;

      this.generating = true;
      this.generation_error = false;
      this.logs = false;

      if(comp.$root.localstore.get('user')){
        uid = JSON.parse(comp.$root.localstore.get('user')).id;
      } else {
        store.emit('logout');
      }

      comp = this;
      $.ajax("/pdf/generate.php",{
        method: "GET",
        data: {
          summary_id: comp.summary.id,
          generated_by: uid
        },
        dataType: "JSON",
        success: function(results){
          comp.generating = false;

          if("logs" in results && results.logs){
            comp.logs = results.logs;
          } else {
            comp.logs = false;
          }
          if(comp.getSummary) comp.getSummary();
        },
        error: function(results){
          comp.generating = false;
          comp.generation_error = "An error occurred while generating. Please try again later.";
        }
      });
    }
  }
}
</script>

<style lang="stylus">
  .ui.grid
    width 100%  
  ol 
    font-size 1.5em
    line-height 2em
    padding-left 0
    cursor pointer
  
    li
      text-overflow ellipsis
      overflow hidden
      list-style-position inside
      white-space nowrap
      &:hover
        text-decoration underline

  h1.header
    margin-top 2em !important
  .download 
    text-decoration underline
    font-weight bold
    float right
  .add-section, .add-footnote
    text-align center
    position relative
    margin-top 5em

    .button
        position absolute
        top -1.5em
        left 50%
        margin-left -75px
        width 150px

  .remove-section
    float right
    font-size 12px

  h2
    .remove-section
      margin-top 10px
  h4
    .remove-section
      margin-top 4px

  .sortable-dragging
    background #ffffff

</style>
