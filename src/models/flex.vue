<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h2 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /><a class='remove-section text-right'>remove</a></h2>
          
          <div class='two fields'>
            <div class='one field'>
              <select class='ui dropdown' v-model="flex.orientation" name='orientation'>
              <option value='P'>Portrait</option>
              <option value='L'>Landscape</option>
              <option value='D'>Data</option>
              </select>
            </div>
          </div>

          <div class='ui ignored info message'>
          Use tables for best formatting of data.
          </div>

          <div class="one field required">
            <div class='unedited'>{{{flex.body}}}</div>
            <textarea disabled v-model="flex.body" name="flex.body" id="flex_{{flex.id}}_body_editor" rows="10" cols="80"></textarea>
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
  props: ['last_update','updateSectionName', 'section', 'flex'],
  watch: {
  },
  ready: function() {
    this.uiForm = $('.ui.form', this.$el).form();
    $(".enable_editor", this.$el).click(this.enableEditor);
  },
  created: function() {
    this.$watch('flex',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
  },
  attached: function() {
    $('.ui.dropdown', this.$el).dropdown();
    $('.ui.dropdown', this.$el).dropdown('setting', 'onChange', this.updateOrientation);
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.flex.updated_at;
    },
    updated_by: function() {
      return (this.flex.updated_by) ? this.flex.updated_by : JSON.parse(this.$root.localstore.get("user"));
    }
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
      var field = editor.name.replace("flex_","").replace("_editor","").split("_")[1];
      var content = editor.getData();
      this.flex[field] = content;
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
    updateOrientation: function(value){
      this.flex.orientation = value;
    },
    update: function(content){
      if(!content || this.$parent.$parent.fetchingSections) return;

      var comp = this;


      //If the editor was changed, content will be a string
      // else content will be the component object.
      var body = (typeof content == "object") ? comp.flex.body : content;
      //replace Office's funny quotes with real ones.
      body = (body) ? body.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"') :  body;


      if(comp.saveTimer) clearTimeout(comp.saveTimer);

      comp.saveTimer = setTimeout(function(){
        store.updateFlex(comp.flex.id, 
                        body,
                        comp.flex.orientation);
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
