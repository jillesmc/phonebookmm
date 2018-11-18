var Signup = {
    init: function () {
        Signup.validateSignupForm();
        Signup.maskPhone();
    },
    validateSignupForm: function () {
        let signupForm = $('#signupForm');

        signupForm.validate({
            rules: {
                name: 'required',
                email: {
                    required: true,
                    email: true,
                },
                phone: {
                    required: true,
                    minlength: 12,
                },
                password: 'required',
                passwordConfirm: {
                    required: true,
                    equalTo: '#inputPassword',
                },
            },
            messages: {
                name: 'Campo requerido',
                email: {
                    required: 'Campo requerido',
                    email: 'E-mail inválido',
                },
                phone: {
                    required: 'Campo requerido',
                    minlength: 'Telefone incompleto',
                },
                password: 'Campo requerido',
                passwordConfirm: {
                    required: 'Campo requerido',
                    equalTo: 'Senhas não conferem',
                },
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
                    url: "/users",
                    data: JSON.stringify({
                        name : form.name.value,
                        email : form.email.value,
                        phone : form.phone.value,
                        password : form.password.value
                    }),
                    success: function( result ) {
                        // form.submit();
                        Signup.flashAlertMessage([
                            ['success', 'Usuário criado com sucesso! Redirecionando para login...']
                        ]);

                        setTimeout(function () {
                            window.location.href = '/login';
                        }, 4000);
                    },
                    complete: function (xhr, textStatus) {
                        console.log(xhr.status);
                    }
                });
            }
        });
    },
    maskPhone: function (field) {
        let phoneInput = field || $('#inputPhone');
        phoneInput.mask('00 0000-00000', {
            onKeyPress: function (phoneNumber, e, field, options) {
                let masks = ['00 0000-00000', '00 00000-0000'];
                let mask = (phoneNumber.length > 12) ? masks[1] : masks[0];
                field.mask(mask, options);
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
    Signup.init();
});