var adminSignin = {
    init: function () {
        adminSignin.validateAdminSigninForm();
    },
    validateAdminSigninForm: function () {
        let signinForm = $('#adminSigninForm');

        signinForm.validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: 'required',
            },
            messages: {
                email: {
                    required: 'Campo requerido',
                    email: 'E-mail inválido',
                },
                password: 'Campo requerido',
            },
            errorElement: 'small',
            errorClass: 'is-invalid',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                error.insertAfter(element);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass(errorClass);
            },
            submitHandler: function (form) {

                $.ajax({
                    type: 'POST',
                    contentType: "application/json",
                    url: "/admin/login/auth",
                    data: JSON.stringify({
                        email : form.email.value,
                        password : form.password.value
                    }),
                    success: function( response ) {
                        sessionStorage.setItem('admin_session',JSON.stringify(response.data));
                        window.location.href = '/admin/dashboard';
                    },
                    error: function (xhr, textStatus) {
                        switch (xhr.status) {
                            case 403:
                                FlashMessage.show([
                                    ['danger', xhr.responseJSON.message]
                                ]);
                                break;
                        }

                    }
                });
            }
        });
    }
};


$(document).ready(function () {
    adminSignin.init();
});