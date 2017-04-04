<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h2 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /><a class='remove-section text-right'>remove</a></h2>
          

          <h4 class="ui dividing header">Profit/Loss Statements and Graphs</h4>
          
          <div class="one field">
            <label>Profit/Loss CSV <span class='uploading' v-if="uploadPercent">(<span v-if="uploadPercent < 100">Uploading: {{uploadPercent}}%</span><span v-if="uploadPercent >= 100">Upload Complete!</span>)</span></label>
            <a href="{{financial.file}}" v-if="financial.file" class='financial_view' target="_blank">View/Download</a>
            <label v-if="financial.file">Replace File</label>
            <input type="hidden" v-model="financial.file" name="financial.file" />
            <input name='file' type='file' class='financial-file-upload'></input>
          </div>

          <div class="one field">
            <label>Output Template</label>
            <select class='ui dropdown' name="output_template" v-model="financial.output_template">
              <option value="-1">--Select a Template--</option>
              <option value="graph">Graphs</option>
              <option value="table">Tables</option>
              <option value="both">Graph/Table</option>
            </select>
          </div>

          <div class='two fields'>
            <div class="one field graph">
              <label>Select Graph</label>
              <select class='ui dropdown' name="graph1" v-model="financial.graph1">
                <option>A Graph</option>
                <option>A Diff Graph</option>
              </select>
              <input type='hidden' for='graph1' name='graph1-data' v-model="financial.graph1"/>
            </div>

            <div class="one field graph">
              <label>Select Second Graph</label>
              <select class='ui dropdown' name="graph2" v-model="financial.graph2">
                <option>A Graph</option>
                <option>A Diff Graph</option>
              </select>
              <input type='hidden' for='graph2' name='graph2-data' v-model="financial.graph2"/>
            </div>

            <div class="one field table">
               <label>Select Table</label>
             <select class='ui dropdown' name="table1" v-model="financial.table1">
                <option>A Table</option>
                <option>Another Table</option>
              </select>
            </div>

            <div class="one field table">
              <label>Select Second Table</label>
              <select class='ui dropdown' name="table2" v-model="financial.table2">
                <option>A Table</option>
                <option>Crazy table</option>
              </select>
            </div>
          </div>
          <div class='two fields graphs'>
            <div class='one field'>
              <div id='graph1' class='graph_output'><img src="{{financial.graph1_data}}" /></div>
            </div>
            <div class='one field'>
              <div id='graph2' class='graph_output'><img src="{{financial.graph2_data}}" /></div>
            </div>
          </div>
          <div class='text-right'>
            <small v-if="updated">Last updated <timeago time="{{updated}}"></timeago> by {{updated_by | realName}}</small>
          </div>
          
          
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var store = require('../store')


module.exports = {
  replace: true,
  props: ['last_update','updateSectionName', 'section', 'financial','uploadPercent'],
  watch: {},
  ready: function() {
    this.uiForm = $('.ui.form', this.$el).form();
    $(".graph, .table").hide();
    $("select[name=graph1],select[name=graph2]", this.$el).change(this.toggleGraphs);
    this.toggleComponents(this.financial.output_template);
	$(".financial-file-upload", this.$el).change(this.uploadFile);
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.financial.updated_at;
    },
    updated_by: function() {
      return (this.financial.updated_by) ? this.financial.updated_by : JSON.parse(this.$root.localstore.get("user"));
    }
  },
  created: function() {
    this.$watch('financial',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
  },
  attached: function() {
    $('[name=output_template]', this.$el).dropdown({
      onChange: this.toggleComponents
    });

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
    update: function(){
      var comp = this;
      if(this.$parent.$parent.fetchingSections) return;

      store.updateFinancialV2(comp.financial.id, 
            comp.financial.csvbody, comp.financial.output_template, comp.financial.graph1, comp.financial.graph2, comp.financial.graph1_data, comp.financial.graph2_data, comp.financial.table1, comp.financial.table2);

      this.last_update = new Date().getTime()/1000;
    },
	uploadFile: function(){
      comp = this;

      var formData = new FormData();

      // HTML file input, chosen by user
      formData.append("file", $(".financial-file-upload").get(0).files[0]);
    
      $.ajax({
        url: "/scripts/csv_upload.php",
        type: "POST",
        data: formData,
        dataType: 'JSON',
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        success: function(res){

          setTimeout(function(){
            comp.uploadPercent = false;
          }, 1000);

          if(!res){
            alert("Unknown error during file upload.");
            return;
          }

          if("filelink" in res){
            comp.financial.file = res.filelink;
          } else {
            alert("Upload was not successful.");
          }

        }, 
        xhr: function(){
          var xhr = new window.XMLHttpRequest();
          //Upload progress
          xhr.upload.addEventListener("progress", function(evt){
            if (evt.lengthComputable) {
              var percentComplete = evt.loaded / evt.total;
              //Do something with upload progress
              comp.uploadPercent = (percentComplete * 100).toFixed(1);
            }
          }, false);

          return xhr;
        }
      });

    },
    toggleGraphs: function(ev){
      var comp = this;
      var $el = $(ev.currentTarget);
      var val = $el.val();

      // Create the data table.
      var data = new google.visualization.DataTable();
      

      var id = $el.attr('name');
      switch(val){
        case "A Graph":
          data.addColumn('string', 'Topping');
          data.addColumn('number', 'Slices');
          data.addRows([
            ['Mushrooms', 3],
            ['Onions', 1],
            ['Olives', 1],
            ['Zucchini', 1],
            ['Pepperoni', 2]
          ]);

          // Set chart options
          var options = {'title':'How Much Pizza I Ate Last Night',
                         'width':600,
                         'height':400};
          var chart = new google.visualization.PieChart(document.getElementById(id));


          break;
        case "A Diff Graph":

          var data = google.visualization.arrayToDataTable([
            ["Element", "Density", { role: "style" } ],
            ["Copper", 8.94, "#b87333"],
            ["Silver", 10.49, "silver"],
            ["Gold", 19.30, "gold"],
            ["Platinum", 21.45, "color: #e5e4e2"]
          ]);

          var view = new google.visualization.DataView(data);
          view.setColumns([0, 1,
                           { calc: "stringify",
                             sourceColumn: 1,
                             type: "string",
                             role: "annotation" },
                           2]);

          var options = {
            title: "Density of Precious Metals, in g/cm^3",
            width: 600,
            height: 400,
            bar: {groupWidth: "95%"},
            legend: { position: "none" },
          };
          var chart = new google.visualization.BarChart(document.getElementById(id));


          break;
      }

      google.visualization.events.addListener(chart, 'ready', function () {
        document.getElementById(id).innerHTML = '<img src="' + chart.getImageURI() + '">';
        comp.financial[id] = val;
        comp.financial[id+"_data"] = chart.getImageURI();
        comp.update();
      });

      // Instantiate and draw our chart, passing in some options.
      chart.draw(data, options);
    },
    toggleComponents: function(type){
      var $el = this.$el;
      this.financial.output_template = type;

      switch(type){
        case "table":
          $(".graph, .table", this.$el).hide();
          $("[name=table1]", this.$el).closest(".table").show();
          $("[name=table2]", this.$el).closest(".table").show();
          break;
        case "graph":
          $(".graph, .table", this.$el).hide();
          $("[name=graph1]", this.$el).closest(".graph").show();
          $("[name=graph2]", this.$el).closest(".graph").show();
          break;
        case "both":
          $(".graph, .table", this.$el).hide();
          $("[name=table1]", this.$el).closest(".table").show();
          $("[name=graph1]", this.$el).closest(".graph").show();
          break;
      }
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

  
  .remove-section
    position relative
    top -2.5em
</style>
