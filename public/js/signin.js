var Signin = {
    init: function () {
        Signin.validateSigninForm();
    },
    validateSigninForm: function () {
        let signinForm = $('#signinForm');

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
                    url: "/login/auth",
                    data: JSON.stringify({
                        email : form.email.value,
                        password : form.password.value
                    }),
                    success: function( result ) {
                        window.location.href = '/app';
                    },
                    complete: function (xhr, textStatus) {
                        console.log();
                        switch (xhr.status) {
                            case 403:
                                Signin.flashAlertMessage([
                                    ['danger', 'Usuário e senha inválidos']
                                ]);
                                break;
                        }

                    }
                });
            }
        });
    },
    flashAlertMessage: function ($arrayMessages) {
        let alertContainer = $('.alert-container');

        $arrayMessages.forEach((item) => {
            let alertClass = item[0]; //success, danger, warning, info
            let message = item[1]; //the message
            let messageHtml = '<div class="alert alert-' + alertClass + '" role="alert">' +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '<span>' + message + '</span>' +
                '</div>';
            let messageElem = $(messageHtml);

            alertContainer.prepend(messageElem);
            window.setTimeout(function () {
                messageElem.fadeTo(500, 0).slideUp(500, function () {
                    $(this).remove();
                });
            }, 4000);
        });
    },
};

$(document).ready(function () {
    Signin.init();
});