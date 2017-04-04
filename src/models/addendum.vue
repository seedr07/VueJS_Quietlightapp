<template>
  <div class='ui one column centered grid'>
    <div class="one column centered row">
      <div class="column">
        <div class="ui form">
          <div class="ui info message hide"></div>
          <h4 class="ui dividing header"><input class='sectionname' type='text' v-model="section.name" /><a class='remove-section text-right'>remove</a></h4>
          

          <div class="one field">
            <label>PDF File <span class='uploading' v-if="uploadPercent">(<span v-if="uploadPercent < 100">Uploading: {{uploadPercent}}%</span><span v-if="uploadPercent >= 100">Upload Complete!</span>)</span></label>
            <a href="{{addendum.file}}" v-if="addendum.file" class='addendum_view' target="_blank">View/Download</a>
            <label v-if="addendum.file">Replace File</label>
            <input type="hidden" v-model="addendum.file" name="addendum.file" />
            <input name='file' type='file' class='addendum-file-upload'></input>
          </div>

          <div class="one field ">
            <label>File Description (for reference only)</label>
            <input type='text' lazy v-model="addendum.description" name="addendum.description"></input>
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
var store = require('../store');

module.exports = {
  replace: true,
  props: ['last_update','updateSectionName', 'section', 'addendum','uploadPercent'],
  watch: {  },
  ready: function() {
    $(".addendum-file-upload", this.$el).change(this.uploadFile);
  },
  created: function() {
    this.$watch('addendum',this.update,{deep: true});
    this.$watch('section', this.updateSectionName,{deep: true});
  },
  attached: function() {
  },
  computed: {
    updated: function () {
      return (this.last_update) ? this.last_update : this.addendum.updated_at;
    },
    updated_by: function() {
      return (this.addendum.updated_by) ? this.addendum.updated_by : JSON.parse(this.$root.localstore.get("user"));
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
    update: function(){
      if(this.$parent.$parent.fetchingSections) return;
      store.updateAddendum(this.addendum.id, 
                        this.addendum.description,
                        this.addendum.file);
      this.last_update = new Date().getTime()/1000;
    },
    uploadFile: function(){
      comp = this;

      var formData = new FormData();

      // HTML file input, chosen by user
      formData.append("file", $(".addendum-file-upload").get(0).files[0]);
    
      $.ajax({
        url: "/scripts/upload.php",
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
            comp.addendum.file = res.filelink;
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

    }
  }
}


</script>

<style lang="stylus">
  .addendum_view
    display inline-block
    width 200px
    margin 1em 0em
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
