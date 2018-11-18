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
                // @to-do trocar para ajax
                form.submit();
            }
        });
    }
};


$(document).ready(function () {
    adminSignin.init();
});