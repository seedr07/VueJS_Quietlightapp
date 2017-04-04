<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h2 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /></h2>
          <div class="one field">
            <div class='unedited'>{{{disclosure.body}}}</div>
            <textarea v-model="disclosure.body" name="disclosure.body" id="disclosure_body_editor" rows="10" cols="80" disabled></textarea>
            <div class='ui button enable_editor'>Edit</div>
          </div>
          <div class="two fields">
            <div class="one field">
                <div id="use_standard" class="ui button">Load Standard Disclosures</div>
            </div>
            <div class="one field">
              <div class='text-right'>
                <small v-if="updated">Last updated <timeago time="{{updated}}"></timeago> by {{updated_by | realName}}</small>
              </div>
            </div>
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
  props: ['last_update','updateSectionName', 'section', 'disclosure'],
  created: function(){
    this.$watch('disclosure',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
  },
  ready: function() {
    $("#use_standard").click(this.loadStandard);
    $(".enable_editor",this.$el).click(this.enableEditor);
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.disclosure.updated_at;
    },
    updated_by: function() {
      return (this.disclosure.updated_by) ? this.disclosure.updated_by : JSON.parse(this.$root.localstore.get("user"));
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
    loadStandard: function(){
      comp = this;
      if(!$("#disclosure_body_editor").parent().is(".redactor-box"))
        this.enableEditor({currentTarget: $(this.$el).find('.button.enable_editor')});
        store.getConfigByKey("disclosures", function(error, result){
        if("value" in result){
          setTimeout(function(){
            $('#disclosure_body_editor').redactor('code.set', result.value);
            comp.disclosure.body = result.value;
          }, 100);
        }
      });
    },
    editorUpdated: function(ev){
      var editor = ev.editor;
      var field = editor.name.replace("disclosure_","").replace("_editor","");
      var content = editor.getData();
      this.disclosure[field] = content;
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
      var body = (typeof content == "object") ? comp.disclosure.body : content;
      //replace Office's funny quotes with real ones.
      body = body.replace(/[\u2018\u2019]/g, "'").replace(/[\u201C\u201D]/g, '"');


      comp.saveTimer = setTimeout(function(){
        store.updateDisclosure(comp.disclosure.id, 
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
</style>
