function indexFormValidation(){

   /* $('.checkbox :checkbox').rcSwitcher({

        inputs:false,
        onText: 'Yes',
        offText: 'No',
        theme: 'modern'

    });*/

    var validator =  $(".registration").validate({

        rules: {

            "Core_userbundle_user[globalOption][name]": {required: true},
            "Core_userbundle_user[profile][mobile]": {
                required: true,
                remote: Routing.generate('bindu_signup_check')
            },
            "Core_userbundle_user[globalOption][location]": {required: true},
            "Core_userbundle_user[globalOption][status]": {required: true}
        },

        messages: {

            "Core_userbundle_user[globalOption][name]":"Enter your full name",
            "Core_userbundle_user[profile][mobile]":{
                required: "Enter valid mobile no",
                remote: "This mobile no is already registered."
            },
            "Core_userbundle_user[globalOption][location]": "Enter your location",
            "Core_userbundle_user[globalOption][status]": "Please read terms & condition and agree",
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

}

