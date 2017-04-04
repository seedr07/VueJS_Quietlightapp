<template>
  <div class='ui one column centered grid'>
    <div class="three column centered row">
      <div class="column">
        <form class="ui form">
          
          <h2 class="ui dividing header">Please Log In</h2>

          <div class="two fields">
            <div class="field">
              <label>Email</label>
              <div class="field">
                <input type="text" name="email" placeholder="you@website.com">
              </div>
            </div>
            <div class="field">
              <label>Password</label>
              <div class="field">
                <input type="password" name="password">
              </div>
            </div>
          </div>

          <div class="ui submit primary button">Login</div>

          <div class="ui error message">
            <div class="header">We noticed some issues</div>
          </div>
        
        
        </form>
      </div>
    </div>
  </div>
</template>

<script>
var store = require('../store')

var rules = {
  email: {
    identifier: 'email',
    rules: [{ type: 'email', prompt: 'That email address is not valid' }]
  },
  password: {
    identifier: 'password',
    rules: [{ type: 'empty', prompt: 'Please enter your password'}]
  }
};

var settings = {
  onSuccess : function(ev) {
    store.emit('login');
    ev.preventDefault();
    return false;
  }
};

module.exports = {
  replace: true,
  props: ['params'],
  data: function () {
    return {
    }
  },
  watch: {
  },
  ready: function() {
    this.uiForm = $('.ui.form').form(rules, settings);
  },
  compiled: function () {
    store.on('login', this.doLogin)
    store.on('loginIncorrect', this.loginWasIncorrect)
  },
  destroyed: function () {
    store.removeListener('login', this.doLogin)
    store.removeListener('loginIncorrect', this.loginWasIncorrect)
  },
  components: {
  },
  methods: {
    doLogin: function () {
      var comp = this;
      var email = $(this.$el).find('[name=email]').val();
      var password = $(this.$el).find('[name=password]').val();
      store.login(email, password, function(error, response){
        if("error" in response) console.log("Wrong Username!");
        else window.location = comp.$parent.router.setRoute("/");
;
      });
    },
    loginWasIncorrect: function () {
      $errors = $("<ul>").addClass("list");
      $error = $("<li>").html("Incorrect username or password.");
      $errors.append($error);
      this.uiForm.find(".ui.error").html($errors).show();
    }
  }
}


</script>

<style lang="stylus">

</style>
