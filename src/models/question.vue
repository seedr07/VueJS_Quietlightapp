<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h4 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /><a class='remove-section text-right'>remove</a></h4>

          <div class="one field required">
            <label>Header</label>
            <select placeholder="Select or Enter a Header..." name="header" v-model="question.header" class="ui fluid search dropdown">
              <option>--None--</option>
              <option v-repeat="header:headers" v-class="selected:header==question.header">{{header}}</option>
            </select>
          </div>

          <div class="one field required">
            <label>Question</label>
            <input type='text' lazy v-model="question.question" name="question.question"></input>
          </div>

          <div class="one field required">
            <label>Answer</label>
            <div class='unedited'>{{{question.answer}}}</div>
            <textarea disabled lazy v-model="question.answer" name="question.answer" id="question_{{question.id}}_answer_editor" rows="10" cols="80"></textarea>
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
  props: ['last_update','updateSectionName', 'section', 'question','headers'],
  watch: {
  },
  ready: function() {
    this.uiForm = $('.ui.form', this.$el).form();
    $(".enable_editor", this.$el).click(this.enableEditor);
    $('.ui.dropdown',this.$el).dropdown({
      allowAdditions: true,
      onChange: this.updateHeader
    });
  },
  created: function() {
    this.$watch('question',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.question.updated_at;
    },
    updated_by: function() {
      return (this.question.updated_by) ? this.question.updated_by : JSON.parse(this.$root.localstore.get("user"));
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
      var field = editor.name.replace("question_","").replace("_editor","").split("_")[1];
      var content = editor.getData();
      this.question[field] = content;
    },
    enableEditor: function(ev){
      var edit = $(ev.currentTarget).parent().find("textarea").prop('id');
      $(ev.currentTarget).parent().find("textarea").prop('disabled',false);
      $(ev.currentTarget).parent().find('.unedited').remove();
      $(ev.currentTarget).remove();
      instance = CKEDITOR.replace(edit);
      instance.on('blur', this.editorUpdated);
    },
    updateHeader: function(value){
      this.question.header = (value == "--None--") ? "" : value;
      this.update();
    },
    update: function(){
      if(this.$parent.$parent.fetchingSections) return;
      store.updateQuestion(this.question.id,
                        this.question.header, 
                        this.question.question,
                        this.question.answer);

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
