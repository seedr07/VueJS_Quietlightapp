<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h2 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /><a class='remove-section text-right'>remove</a></h2>

          <h4 class="ui dividing header">Traffic Data and Graphs</h4>
          
          <div class='ui ignored info message'>
          Use tables for best formatting of data.
          All embeded images will be included below any text and tables.
          </div>

          <div class="one field required">
            <div class='unedited'>{{{webtraffic.body}}}</div>
            <textarea disabled v-model="webtraffic.body" name="webtraffic.body" id="webtraffic_body_editor" rows="10" cols="80"></textarea>
            <div class='ui button enable_editor'>Edit</div>
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
  props: ['last_update','updateSectionName', 'section', 'webtraffic'],
  watch: {},
  ready: function() {
    $(".enable_editor", this.$el).click(this.enableEditor);
  },
  created: function() {
    this.$watch('webtraffic',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.webtraffic.updated_at;
    },
    updated_by: function() {
      return (this.webtraffic.updated_by) ? this.webtraffic.updated_by : JSON.parse(this.$root.localstore.get("user"));
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
  },
  methods: {
    editorUpdated: function(ev){
      var editor = ev.editor;
      var field = editor.name.replace("webtraffic_","").replace("_editor","");
      var content = editor.getData();
      this.webtraffic[field] = content;
    },
    enableEditor: function(ev){
      var $edit = $(ev.currentTarget).parent().find("textarea");
      $edit.prop('disabled',false);
      $(ev.currentTarget).parent().find('.unedited').remove();
      $(ev.currentTarget).remove();
      $edit.redactor({
          focus: true,
          plugins: ['table','imagemanager'],
          imageUpload: '/scripts/upload.php',
          imageManagerJson: '/scripts/imagelist.php',
          changeCallback: this.update
      });
    },
    update: function(content){
      if(!content || this.$parent.$parent.fetchingSections) return;

      var comp = this;

      if(comp.saveTimer) clearTimeout(comp.saveTimer);


      //If the editor was changed, content will be a string
      // else content will be the component object.
      var body = (typeof content == "object") ? comp.webtraffic.body : content;
      //replace Office's funny quotes with real ones.
      body = body.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"');


      comp.saveTimer = setTimeout(function(){
       store.updateWebTraffic(comp.webtraffic.id, 
                        body);
      }, 400);
      this.last_update = new Date().getTime()/1000;
     
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
