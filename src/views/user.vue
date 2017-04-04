<template>
  <div class='ui one column centered grid'>
    <div class="two column centered row">
      <div class="column">
        <form class="ui form" >
          <input type='hidden' name='user_id' v-model="edit_user.id" />
          <div class="ui info message hide"></div>
          <h4 class="ui dividing header">Personal Information</h4>
          <div class="one field">
            <div class="required field">
              <label>Name</label>
              <div class="two fields">
                <div class="field">
                  <input type="text" name="first_name" v-model="edit_user.first_name" placeholder="First Name">
                </div>
                <div class="field">
                  <input type="text" name="last_name" v-model="edit_user.last_name" placeholder="Last Name">
                </div>
              </div>
            </div>
          </div>
          <div class="one field">
            <div class="required field">
              <label>Contact</label>
              <div class="two fields">
                <div class="field">
                  <input type="text" name="phone" v-model="edit_user.phone" placeholder="555-555-5555">
                </div>
                <div class="field">
                  <input type="text" name="email" v-model="edit_user.email" placeholder="Email">
                </div>
              </div>
            </div>
          </div>          
          <h4 class="ui dividing header">Settings</h4>
          <div class="one field" v-if="params.user.is_admin | parseInt">
            <div class="field">
              <div class="two fields">
                <div class="ui toggle checkbox">
                  <input type="checkbox" name="is_admin" v-model="edit_user.is_admin | parseInt">
                  <label>Is Admin</label>
                </div>
              </div>
            </div>
          </div>
          <h4 class="ui dividing header">Account Info</h4>
          <div class="two fields">
            <div class="required field">
              <label>Username</label>
              <div class="ui icon input">
                <input readonly type="text" name='email' v-model="edit_user.email" placeholder="email@domain.com">
                <i class="user icon"></i>
              </div>
            </div>
            <div class="required field">
              <label>Password</label>
              <div class="ui icon input">
                <input name='password' type="password">
                <i class="lock icon"></i>
              </div>
            </div>
          </div>

          <h2 class="ui dividing header"></h2>
          
          <div class="ui submit button">
            <span v-if="edit_user.id == 'add'">Create</span>
            <span v-if="edit_user.id != 'add'">Update</span>
          </div>

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

var settings = {
  fields: {
    email: {
      identifier: 'email',
      rules: [{ type: 'email', prompt: 'That email address is not valid' }]
    },
    phone: {
      identifier: 'phone',
      rules: [{ type: 'empty', prompt: 'Please enter a phone number' }]
    },
    first: {
      identifier: 'first_name',
      rules: [{ type: 'empty', prompt: 'Please enter your first name' }]
    },
    last: {
      identifier: 'last_name',
      rules: [{ type: 'empty', prompt: 'Please enter your last name' }]
    },
    password: {
      identifier: 'password',
      rules: [{ type: 'empty', prompt: 'Please enter a password' }, 
              { type: 'length[8]', prompt: 'Password must be at least 8 characters.'}]
    }
  },
  onSuccess : function(ev) {
    if($(ev.target).find("[name='user_id']").val() == "add") store.emit('addUser');
    else store.emit('updateUser');
    ev.preventDefault();
    return false;
  }
};

module.exports = {
  replace: true,
  props: ['params','user_id'],
  data: function () {
    return {
      user_id: false,
      edit_user: {
        id: 'add',
        first_name: null,
        last_name: null,
        email: null,
        password: null,
        phone: null
      }
    }
  },
  watch: {
    
  },
  ready: function() {
    if(this.params.user_id != "add") delete settings.fields.password;
    this.uiForm = $('.ui.form').form(settings);
    $(".ui.checkbox").checkbox();
    this.$watch('params', this.getUser, {deep: true});
  },
  attached: function() {
    if(this.params.user_id && this.params.user_id != "add") this.getUser();
  },
  compiled: function () {
    store.on('updateUser', this.updateUser)
    store.on('addUser', this.addUser)
  },
  detached: function () {
    this.edit_user = { id: 'add', first_name: null, last_name: null, email: null, password: null, phone: null }; //reset to default  
    this.uiForm.find(".ui.info,.ui.error").hide(); //hide errors/success
    this.uiForm.form('clear'); //clear errors
  },
  destroyed: function () {
    store.removeListener('updateUser', this.updateUser)
    store.removeListener('addUser', this.addUser)
  },
  components: {
  },
  methods: {
    getUser: function(){
      comp = this;
      user_id =  this.params.user_id;
      if(user_id == "add") {
        comp.edit_user = {
          id: "add"
        }
        return;
      }
      store.user.get(user_id, function(error, result){
        if(result) comp.edit_user = result;  
      });
    },
    updateUser: function () {
      var email = $(this.$el).find('[name=email]').val();
      var password = $(this.$el).find('[name=password]').val();
      var first_name = $(this.$el).find('[name=first_name]').val();
      var last_name = $(this.$el).find('[name=last_name]').val();
      var phone = $(this.$el).find('[name=phone]').val();

      var is_admin = ($(this.$el).find('[name=is_admin]')) ? ($(this.$el).find('[name=is_admin]').is(":checked") | 0) : null;
      var comp = this;

      //Hide errors
      comp.uiForm.find(".ui.error").hide();

      store.user.update(this.params.user_id, email, password, first_name, last_name, phone, is_admin, function(error, response){
        if("error" in response) {
          console.log("Failed to update user!");
          $errors = $("<ul>").addClass("list");
          $error = $("<li>").html("There was a problem updating your account.");
          $error2 = $("<li>").html(response.error.message);
          $errors.append($error).append($error2);
          comp.uiForm.find(".ui.error").html($errors).show();
        } else {
          comp.uiForm.find(".ui.info").html("User updated!").show();
          
        }
      });
    },
    addUser: function () {
      var email = $(this.$el).find('[name=email]').val();
      var password = $(this.$el).find('[name=password]').val();
      var first_name = $(this.$el).find('[name=first_name]').val();
      var last_name = $(this.$el).find('[name=last_name]').val();
      var phone = $(this.$el).find('[name=phone]').val();

      var is_admin = ($(this.$el).find('[name=is_admin]')) ? ($(this.$el).find('[name=is_admin]').is(":checked") | 0) : null;
      var comp = this;

      //Hide errors
      comp.uiForm.find(".ui.error").hide();

      store.user.create(email, password, first_name, last_name, phone, is_admin, function(error, response){
        if("error" in response) {
          console.log("Failed to update user!");
          $errors = $("<ul>").addClass("list");
          $error = $("<li>").html("There was a problem updating your account.");
          $error2 = $("<li>").html(response.error.message);
          $errors.append($error).append($error2);
          comp.uiForm.find(".ui.error").html($errors).show();
        } else {
          comp.uiForm.find(".ui.info").addClass('user-created').html("User created!").show();
          setTimeout(function(){
            $(".user-created").remove();
          }, 2000);
          comp.$parent.router.setRoute('/user/'+response.id);
        }
      });
    },
  }
}


</script>

<style lang="stylus">

</style>
