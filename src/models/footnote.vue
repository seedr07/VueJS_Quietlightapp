<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h4 class="ui dividing header"><small><i class="sidebar icon"></i></small>Question/Answer <a class='remove-section text-right'>remove</a></h4>
          
          <div class="one field required">
            <label>Question</label>
            <input type='text' lazy v-model="question.question" name="question.question"></input>
          </div>

          <div class="one field required">
            <label>Footnote</label>
            <div class='unedited'>{{{question.answer}}}</div>
            <textarea disabled lazy v-model="question.answer" name="question.answer" id="question_{{question.id}}_answer_editor" rows="10" cols="80"></textarea>
            <div class='ui button enable_editor'>Edit</div>
          </div>
          <div class='text-right'>
            <small v-if="question.updated_at">Last updated <timeago time="{{question.updated_at}}"></timeago> by {{question.updated_by | realName}}</small>
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
  props: ['updateSectionName', 'section', 'question'],
  watch: {
  },
  ready: function() {
    $(".enable_editor", this.$el).click(this.enableEditor);
  },
  created: function() {
    this.$watch('question',this.update,{deep: true});
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
    update: function(){
      store.updateQuestion(this.question.id, 
                        this.question.question,
                        this.question.answer);
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
