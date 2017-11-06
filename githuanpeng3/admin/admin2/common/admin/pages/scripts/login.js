/**
 * Created by hantong on 16/6/27.
 */

var Login = function() {

    var handleLogin = function() {
        function errorNotice(text, element){
            var id = element.attr('name') + '-error';
            $(id).remove();

            element.parents('.form-group').addClass('has-error');
            var error = $('<span id="'+id+'" class="help-block">'+text+'</span>');
            error.insertAfter(element.closest('.input-icon'));
        }
        function clearNotice(element){
            element.closest('.input-icon').after('.help-block').remove();
            element.parents('.form-group').removeClass('has-error');
        }
        $('.login-form').validate({
            debug:true  ,
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                username: {
                    required: "请填写用户名"
                },
                password: {
                    required: "密码不能为空"
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit
                $('.alert-danger', $('.login-form')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                var userName = $('input[name=username]');
                var password = $('input[name=password]');
                //form.submit(); // form validation success, call ajax form submit
                $.ajax({
                    url:$conf.api + 'login.php',
                    type:'post',
                    dataType:'json',
                    data:$('.login-form').serialize(),
                    success:function(d){
                        if(d.stat == 1){
                            location.href = location.href;
                        }else{
                            if(d.err.code){
                                $('.alert-danger', $('.login-form')).show();
                                $('.alert-danger', $('.login-form')).find('span').html('用户名或密码错误');
                            }
                        }
                    }
                });
            }
        });

        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    $('.login-form').submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    }

    var handleForgetPassword = function() {
        $('.forget-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },

            messages: {
                email: {
                    required: "Email is required."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit

            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                form.submit();
            }
        });

        $('.forget-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.forget-form').validate().form()) {
                    $('.forget-form').submit();
                }
                return false;
            }
        });

        jQuery('#forget-password').click(function() {
            jQuery('.login-form').hide();
            jQuery('.forget-form').show();
        });

        jQuery('#back-btn').click(function() {
            jQuery('.login-form').show();
            jQuery('.forget-form').hide();
        });

    }

    return {
        //main function to initiate the module
        init: function() {

            handleLogin();
            handleForgetPassword();
        }

    };

}();
