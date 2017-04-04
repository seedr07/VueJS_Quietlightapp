<template>
	<div class="ui fluid search selection dropdown input"  v-class='loading:!advisors'>
	  <input type="hidden" name="{{name}}" v-model="select">
	  <i class="dropdown icon"></i>
	  <input class="search">
	  <div class="default text">Select an Advisor...</div>
	  <div class="menu">
	    <div v-repeat="advisor:advisors" data-value="{{advisor.id}}" v-class="selected:advisor.id==select" class="item">{{advisor | realName}}</div>
	  </div>
	</div>
</template>

<script>

var store = require('../store')

module.exports = {
  replace: true,
  props: ['name', 'select', 'advisors'],
  computed: {
  },
  attached: function() {
    $(this.$el).dropdown('setting', 'onChange', this.update);
  },	
  ready: function() {
    comp = this;

    store.user.list(100000,0,function(error, result){
      result = result.users;
      comp.advisors = result;
    	setTimeout(function(){ $(".ui.dropdown").dropdown('forceSelection'); }, 400);
    });

    this.$watch('select',this.update,{deep: true});
  
  },
  methods: {
    update: function(value){
      if(value != this.select) this.$parent.advisorUpdate(value);
    }
  }
}
</script>

<style lang="stylus">
</style>