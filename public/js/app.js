var AppHome = {
    init: function () {
        AppHome.getUserData();
        AppHome.prepareSearchField();
        AppHome.floatTableHeader();
        AppHome.modalViewActions();
        AppHome.loadContacts();
        AppHome.resetModalAfterHide();
        AppHome.loadDataForViewContactModal();
        AppHome.loadDataForEditContactModal();
        AppHome.loadDataForDeleteContactModal();
        AppHome.loadDataForAccountInfoModal();
    },
    prepareSearchField: function () {
        let searchInput = $("#inputSearch");
        let search_cleaner = $(".search__clear-button");

        searchInput.keyup(function () {
            search_cleaner.toggle(Boolean($(this).val()));
            AppHome.searchContacts($(this).val());
        });
        search_cleaner.toggle(Boolean(searchInput.val()));
        search_cleaner.click(function () {
            searchInput.val('').focus();
            AppHome.searchContacts($(this).val());
            $(this).hide();
        });
    },
    searchContacts: function (query) {
        let sessionData = AppHome.loadSession();
        let userId = sessionData.user.id;

        let loader = $('#loader');
        let tableBody = $('.contacts-table tbody');
        tableBody.html('');
        loader.show();

        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            data: {'q': query},
            url: "/users/" + userId + "/contacts",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
            },
            success: function (response) {
                loader.hide();
                AppHome.buildContactTable(response);
            },
            error: function (xhr, textStatus) {
                loader.hide();
                FlashMessage.show([
                    ['success', 'Sua lista de contatos está vazia. Vamos começar']
                ]);
            }
        });
    },
    floatTableHeader: function () {
        let table = $('table.contacts-table');
        table.floatThead({top: 67});
    },
    buildContactTable: function (data) {
        let colors = ['#e6194B', '#3cb44b', '#4363d8', '#f58231', '#911eb4', '#f032e6', '#469990', '#9A6324',
            '#800000', '#808000', '#000075', '#a9a9a9', '#000000'];
        let contactRowHtml = "";

        let i = 0;
        let firstCharacter = null;

        $.each(data, function (index, contact) {
            let lastTimeFirstCharacter = firstCharacter;
            firstCharacter = contact.name.substr(0, 1).toUpperCase();
            let explodedName = contact.name.split(' ');
            let initials = explodedName[0].substr(0, 1);
            if (explodedName.length > 1) {
                initials += explodedName[explodedName.length - 1].substr(0, 1);
            }
            i = i == 12 ? 0 : i;

            let contactPhone = (contact.phone !== null ? contact.phone : '');
            let anchorCharacter = (firstCharacter != lastTimeFirstCharacter ? firstCharacter : '');

            contactRowHtml += '<tr' +
                ' class="contacts-table__row" ' +
                'data-id="' + contact.id + '">' +
                '<td class="contacts-table__first-character" scope="row" data-toggle="modal" ' +
                'data-target="#view-contact-modal"> ' +
                anchorCharacter +
                '</td>' +
                '<td data-toggle="modal" data-target="#view-contact-modal">' +
                '<div class="contacts-table__row-circle" style="background-color: ' + colors[i] + ';">' +
                initials + '</div>' +
                '</td>' +
                '<td data-toggle="modal" data-target="#view-contact-modal">' +
                contact.name +
                '</td>' +
                '<td data-toggle="modal" data-target="#view-contact-modal"' +
                ' class="d-none d-sm-table-cell">' +
                contactPhone +
                '</td>' +
                '<td data-toggle="modal" data-target="#view-contact-modal"' +
                ' class="d-none d-md-table-cell">' +
                contact.email +
                '</td>' +
                '<td class="d-none d-sm-table-cell text-right py-2 pl-0">' +
                '<button type="button" class="btn btn-light btn-sm mr-4"' +
                ' aria-label="Editar" title="Editar"' +
                ' data-toggle="modal" data-target="#edit-contact-modal">' +
                '<span class="oi oi-pencil text-info"></span>' +
                '</button>' +
                '<button type="button" class="btn btn-light btn-sm ml-4"' +
                ' aria-label="Remover" title="Remover"' +
                ' data-toggle="modal" data-target="#delete-contact-modal">' +
                '<span class="oi oi-x text-danger"></span>' +
                '</button>' +
                '</td>' +
                '</tr>';
            i++;
        });

        $('.contacts-table tbody').html(contactRowHtml);
    },
    modalViewActions: function () {
        let viewModal = $('#view-contact-modal');
        let deleteModal = $('#delete-contact-modal');
        let editModal = $('#edit-contact-modal');
        let editButton = $('#view-contact-modal__edit-button');
        let deleteButton = $('#view-contact-modal__remove-button');

        editButton.click(function () {

            viewModal.removeClass('fade');
            editModal.removeClass('fade');
            viewModal.modal('hide');
            editModal.modal('show');

            viewModal.addClass('fade');
            editModal.addClass('fade');

            AppHome.updateEditFormFields();

        });

        deleteButton.click(function () {

            viewModal.removeClass('fade');
            deleteModal.removeClass('fade');
            viewModal.modal('hide');
            deleteModal.modal('show');
            viewModal.addClass('fade');
            deleteModal.addClass('fade');

            AppHome.updateModalContent(deleteModal);
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
    loadSession: function () {
        let sessionData;
        sessionData = JSON.parse(sessionStorage.getItem('user_session'));
        if (!sessionData || !sessionData.user || !sessionData.user.id) {
            window.location.href = '/';
        }
        return sessionData;
    },
    getUserData: function () {

        let sessionData = AppHome.loadSession();
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            url: "/users/" + sessionData.user.id,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + sessionData.jwt);
            },
            success: function (response) {
                let userName = response.name.split(' ')[0]; //firstname
                $('#top-bar .user-name').html(userName);
            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    case 401:
                        FlashMessage.show([
                            ['danger', xhr.responseJSON.message]
                        ]);
                        setTimeout(function () {
                            window.location.href = '/'
                        }, 2000);
                        break;
                }

            }
        });

    },
    loadContacts: function () {
        let sessionData = AppHome.loadSession();
        let user_id = sessionData.user.id;
        let loader = $('#loader');
        loader.show();
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/users/" + user_id + "/contacts",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
            },
            success: function (response) {
                loader.hide();
                if (response.length == 0) {
                    FlashMessage.show([
                        ['success', 'Sua lista de contatos está vazia. Vamos começar']
                    ]);
                }
                AppHome.buildContactTable(response);
            },
            error: function (xhr, textStatus) {
                loader.hide();

            }
        });

    },
    updateEditFormFields: function () {
        let sessionData = AppHome.loadSession();
        let contactId = sessionStorage.getItem('contactId');
        let userId = sessionData.user.id;
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/users/" + userId + "/contacts/" + contactId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
            },
            success: function (response) {
                let editContactForm = $('#editContactForm');
                let addMorePhoneButton = $('.form-contact__btn--add-more-phones');

                editContactForm.find('input[name="name"]').val(response.name);
                editContactForm.find('input[name="email"]').val(response.email);
                editContactForm.find('textarea[name="note"]').val(response.note);

                for (let i = 0; i < response.phones.length; i++) {
                    let index = i !== 0 ? i : '';

                    if (editContactForm.find('input[name="inputPhone[' + index + ']"]').length === 0) {
                        addMorePhoneButton.click();
                    }

                    editContactForm.find('input[name="inputPhone[' + index + ']"]').val(response.phones[i].phone);

                }
            },
            error: function (xhr, textStatus) {
                FlashMessage.show([
                    ['danger', 'Algo não deu certo']
                ]);
            }
        });
    },
    updateAccountFormFields: function () {
        let sessionData = AppHome.loadSession();
        let userId = sessionData.user.id;
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/users/" + userId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
            },
            success: function (response) {
                let accountInfoForm = $('#accountInfoForm');

                accountInfoForm.find('input[name="name"]').val(response.name);
                accountInfoForm.find('input[name="email"]').val(response.email);
                accountInfoForm.find('input[name="phone"]').val(response.phone);

                accountInfoForm.find('input[name="password"]').val('');
                accountInfoForm.find('input[name="passwordPrevious"]').val('');
                accountInfoForm.find('input[name="passwordConfirm"]').val('');

                accountInfoForm.find('input[name="password"]').removeAttr('aria-invalid');
                accountInfoForm.find('input[name="password"]').removeAttr('aria-describedby');
                accountInfoForm.find('input[name="password"]').removeClass('is-invalid');
                accountInfoForm.find('input[name="password"]').next('small').remove();

                accountInfoForm.find('input[name="passwordPrevious"]').removeAttr('aria-invalid');
                accountInfoForm.find('input[name="passwordPrevious"]').removeAttr('aria-describedby');
                accountInfoForm.find('input[name="passwordPrevious"]').removeClass('is-invalid');
                accountInfoForm.find('input[name="passwordPrevious"]').next('small').remove();

                accountInfoForm.find('input[name="passwordConfirm"]').removeAttr('aria-invalid');
                accountInfoForm.find('input[name="passwordConfirm"]').removeAttr('aria-describedby');
                accountInfoForm.find('input[name="passwordConfirm"]').removeClass('is-invalid');
                accountInfoForm.find('input[name="passwordConfirm"]').next('small').remove();

            },
            error: function (xhr, textStatus) {
                FlashMessage.show([
                    ['danger', 'Algo não deu certo']
                ]);
            }
        });
    },
    updateModalContent: function (modal) {
        let sessionData = AppHome.loadSession();
        let userId = sessionData.user.id;
        let contactId = sessionStorage.getItem('contactId');
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/users/" + userId + "/contacts/" + contactId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
            },
            success: function (response) {
                let phonesNotesHtml = '';

                phonesNotesHtml += '<div class="mb-4">' +
                    '<h5>' + response.name + '</h5>' +
                    '</div>';

                if (response.email) {
                    phonesNotesHtml += '<p>' +
                        '<div class="float-left pr-3 text-right" ><span class="oi oi-envelope-closed' +
                        ' text-muted"></span></div>' +
                        '<a href="mailto:' + response.email + '" class="">' + response.email + '</a>' +
                        '</p>';
                }

                for (let i = 0; i < response.phones.length; i++) {
                    let numberOnlyPhone = response.phones[i].phone.replace(' ', '').replace('-', '');
                    phonesNotesHtml += '<p>' +
                        '<div class="float-left pr-3 text-right" ><span class="oi oi-phone' +
                        ' text-muted"></span></div>' +
                        '<a href="tel:' + numberOnlyPhone + '">' +
                        response.phones[i].phone +
                        '</a>' +
                        '</p>';
                }

                if (response.note) {
                    phonesNotesHtml += '<p>' +
                        '<div class="float-left pr-3 text-right"><span class="oi oi-book' +
                        ' text-muted"></span></div>' +
                        response.note +
                        '</p>';
                }

                modal.find('.view-contact-data').html(phonesNotesHtml);
            },
            error: function (xhr, textStatus) {
                FlashMessage.show([
                    ['danger', 'Algo não deu certo']
                ]);
            }
        });
    },
    loadDataForViewContactModal: function () {
        let viewContactModal = $('#view-contact-modal');
        viewContactModal.on('show.bs.modal', function (event) {
            let modal = $(event.target);
            let dataContainer;
            if (event.relatedTarget !== undefined) {
                dataContainer = $(event.relatedTarget.closest('tr'));
                sessionStorage.setItem('contactId', dataContainer.data('id'));
            }

            AppHome.updateModalContent(modal);
        });
    },
    loadDataForEditContactModal: function () {

        let editContactModal = $('#edit-contact-modal');
        editContactModal.on('show.bs.modal', function (event) {
            if (event.relatedTarget !== undefined) {
                dataContainer = $(event.relatedTarget.closest('tr'));
                sessionStorage.setItem('contactId', dataContainer.data('id'));
            }

            AppHome.updateEditFormFields();
        });
    },
    loadDataForDeleteContactModal: function () {
        let deleteContactModal = $('#delete-contact-modal');
        deleteContactModal.on('show.bs.modal', function (event) {
            let modal = $('#delete-contact-modal');
            if (event.relatedTarget !== undefined) {
                dataContainer = $(event.relatedTarget.closest('tr'));
                sessionStorage.setItem('contactId', dataContainer.data('id'));
            }
            AppHome.updateModalContent(modal);
        });
    },
    loadDataForAccountInfoModal: function () {

        let accountInfoModal = $('#account-info-modal');
        accountInfoModal.on('show.bs.modal', function (event) {

            AppHome.updateAccountFormFields();
        });
    },
    resetModalAfterHide: function () {
        $('#edit-contact-modal').on('hide.bs.modal', function (e) {
            AppHome.clearForms();
        });
        $('#add-contact-modal').on('hide.bs.modal', function (e) {
            AppHome.clearForms();
        });
        $('#delete-contact-modal').on('hide.bs.modal', function (e) {
            AppHome.clearDeleteContactData();
        });
    },
    clearDeleteContactData: function () {
        let loadingHtml = '<div class="text-center">' +
            '<img src="/images/loading-spinner.svg" alt="" width="100">' +
            '</div>'
        $('#delete-contact-modal').find('.view-contact-data').html(loadingHtml);
    },
    clearForms: function () {
        let editContactForm = $('#editContactForm');
        let addContactForm = $('#addContactForm');

        [editContactForm, addContactForm].forEach(function (formElement) {
            $(formElement).find('.phone--remove').click();
            $(formElement).find('input[name="inputPhone[]"]').val('');
            $(formElement).find('input[name="name"]').val('');
            $(formElement).find('input[name="email"]').val('');
            $(formElement).find('textarea[name="note"]').val('');

            $(formElement).find('input[name="inputPhone[]"]').removeAttr('aria-invalid');
            $(formElement).find('input[name="inputPhone[]"]').removeAttr('aria-describedby');
            $(formElement).find('input[name="inputPhone[]"]').removeClass('is-invalid');
            $(formElement).find('input[name="inputPhone[]"]').next('small').remove();

            $(formElement).find('input[name="name"]').removeAttr('aria-invalid');
            $(formElement).find('input[name="name"]').removeAttr('aria-describedby');
            $(formElement).find('input[name="name"]').removeClass('is-invalid');
            $(formElement).find('input[name="name"]').next('small').remove();

            $(formElement).find('input[name="email"]').removeAttr('aria-invalid');
            $(formElement).find('input[name="email"]').removeAttr('aria-describedby');
            $(formElement).find('input[name="email"]').removeClass('is-invalid');
            $(formElement).find('input[name="email"]').next('small').remove();
        });
    }

};

/*
*
*  Form dynamics, validations and masks
*
* */
var ContactForms = {
    addContactForm: $('#addContactForm'),
    editContactForm: $('#editContactForm'),
    accountInfoForm: $('#accountInfoForm'),

    init: function () {
        ContactForms.dynamicPhone();
        ContactForms.maskPhone();
        ContactForms.maskPhone($('#accountInputPhone'));
        ContactForms.validateAddContactForm();
        ContactForms.validateEditContactForm();
        ContactForms.validateAccountInfoForm();
        ContactForms.deleteContactHandle();
    },
    dynamicPhone: function () {
        let addMorePhoneButton = $('.form-contact__btn--add-more-phones');

        addMorePhoneButton.click(function (event) {
            event.preventDefault();
            let removeHtml = '<button type="button" class="phone--remove btn btn-light"><span class="oi oi-x' +
                ' text-danger"></span></button>';
            let removeButton = $(removeHtml);
            let container = $(this).closest('.row').prev();
            let nextChildIdNumber = container.children().length;
            let clonedPhoneField = container.children().first().clone();
            let inputPhoneField = clonedPhoneField.find('input');

            //prepare new phone input field
            inputPhoneField.attr('name', 'inputPhone[' + nextChildIdNumber + ']');
            inputPhoneField.removeAttr('aria-invalid');
            inputPhoneField.removeAttr('aria-describedby');
            inputPhoneField.val('');
            inputPhoneField.removeClass('is-invalid');
            inputPhoneField.next('small').remove();
            inputPhoneField.parent().prev().remove();

            ContactForms.maskPhone(inputPhoneField);

            clonedPhoneField.find('.input-group').append(removeButton);
            container.append(clonedPhoneField);

            inputPhoneField.rules('add',
                {
                    minlength: 12,
                    messages: {
                        minlength: "Telefone incompleto"
                    }
                });

            ContactForms.attachDelete();
            inputPhoneField.focus();

            ContactForms.addContactForm.validate();
        });
    },
    deleteContactHandle: function () {
        $('#deleteContactButton').click(function (event) {
            let sessionData = AppHome.loadSession();
            let userId = sessionData.user.id;
            let contactId = sessionStorage.getItem('contactId');
            let deleteContactModal = $('#delete-contact-modal');

            $.ajax({
                type: 'DELETE',
                contentType: "application/json",
                dataType: "json",
                url: "/users/" + userId + "/contacts/" + contactId,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
                },
                success: function (response) {
                    deleteContactModal.modal('hide');
                    AppHome.loadContacts();
                    FlashMessage.show([
                        ['success', 'Contato removido com sucesso']
                    ])
                },
                error: function (xhr, textStatus) {
                    FlashMessage.show([
                        ['danger', 'Algo não deu certo']
                    ]);
                }
            });


        });
    },
    attachDelete: function () {
        let deletePhoneButtons = $('.phone--remove');
        deletePhoneButtons.off();
        deletePhoneButtons.click(function () {
            $(this).closest('.row').remove();
        });
    },
    maskPhone: function (field) {
        let phoneInput = field || $('input[name="inputPhone[]"]');
        phoneInput.mask('00 0000-00000', {
            onKeyPress: function (phoneNumber, e, field, options) {
                let masks = ['00 0000-00000', '00 00000-0000'];
                let mask = (phoneNumber.length > 12) ? masks[1] : masks[0];
                field.mask(mask, options);
            }
        });
    },

    validateAddContactForm: function () {
        ContactForms.addContactForm.validate({
            rules: {
                name: 'required',
                email: {
                    email: true,
                },
                'inputPhone[]': {
                    minlength: 12,
                },
                password: 'required',
            },
            messages: {
                name: 'Campo requerido',
                email: {
                    email: 'E-mail inválido',
                },
                'inputPhone[]': {
                    minlength: 'Telefone incompleto',
                },
                password: 'Campo requerido',
            },
            errorElement: 'small',
            errorClass: 'is-invalid',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.next('button').length !== 0)
                    error.appendTo($(element.parent()));
                else
                    error.insertAfter(element);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass(errorClass);
            },
            submitHandler: function (form) {
                let sessionData = AppHome.loadSession();
                let userId = sessionData.user.id;
                let phones = [];

                $(form).find('input[name^="inputPhone"]').each(function () {
                    phones.push($(this).val());
                });

                $.ajax({
                    type: 'POST',
                    contentType: "application/json",
                    url: "/users/" + userId + "/contacts",
                    data: JSON.stringify({
                        name: form.name.value,
                        email: form.email.value,
                        phones: phones,
                        note: form.note.value
                    }),
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
                    },
                    success: function (response) {
                        sessionStorage.setItem('contactId', response.contactId);

                        FlashMessage.show([
                            ['success', 'Contato criado com sucesso']
                        ]);

                        let viewModal = $('#view-contact-modal');
                        let addModal = $('#add-contact-modal');

                        AppHome.loadContacts();
                        addModal.removeClass('fade');
                        viewModal.removeClass('fade');
                        addModal.modal('hide');
                        viewModal.modal('show');
                        addModal.addClass('fade');
                        viewModal.addClass('fade');

                    },
                    complete: function (xhr, textStatus) {
                        console.log(xhr.status);
                    }
                });
            }
        });
    },

    validateEditContactForm: function () {
        ContactForms.editContactForm.validate({
            rules: {
                name: 'required',
                email: {
                    email: true,
                },
                'inputPhone[]': {
                    minlength: 12,
                },
                password: 'required',
            },
            messages: {
                name: 'Campo requerido',
                email: {
                    email: 'E-mail inválido',
                },
                'inputPhone[]': {
                    minlength: 'Telefone incompleto',
                },
                password: 'Campo requerido',
            },
            errorElement: 'small',
            errorClass: 'is-invalid',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.next('button').length !== 0)
                    error.appendTo($(element.parent()));
                else
                    error.insertAfter(element);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass(errorClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass(errorClass);
            },
            submitHandler: function (form) {
                let sessionData = AppHome.loadSession();
                let userId = sessionData.user.id;
                let contactId = sessionStorage.getItem('contactId');
                let phones = [];

                $(form).find('input[name^="inputPhone"]').each(function () {
                    phones.push($(this).val());
                });

                $.ajax({
                    type: 'PUT',
                    contentType: "application/json",
                    url: "/users/" + userId + "/contacts/" + contactId,
                    data: JSON.stringify({
                        name: form.name.value,
                        email: form.email.value,
                        phones: phones,
                        note: form.note.value
                    }),
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
                    },
                    success: function (response) {
                        sessionStorage.setItem('contactId', response.contactId);

                        FlashMessage.show([
                            ['success', 'Contato editado com sucesso']
                        ]);

                        let viewModal = $('#view-contact-modal');
                        let editModal = $('#edit-contact-modal');

                        AppHome.loadContacts();
                        editModal.removeClass('fade');
                        viewModal.removeClass('fade');
                        editModal.modal('hide');
                        viewModal.modal('show');
                        editModal.addClass('fade');
                        viewModal.addClass('fade');

                    },
                    complete: function (xhr, textStatus) {
                        console.log(xhr.status);
                    }
                });
            }
        });
    },

    validateAccountInfoForm: function () {
        ContactForms.accountInfoForm.validate({
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
                passwordPrevious: 'required',
                password: 'required',
                passwordConfirm: {
                    required: true,
                    equalTo: '#accountInputPassword',
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
                passwordPrevious: 'Campo requerido',
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
                let sessionData = AppHome.loadSession();
                let userId = sessionData.user.id;

                $.ajax({
                    type: 'PUT',
                    contentType: "application/json",
                    url: "/users/" + userId,
                    data: JSON.stringify({
                        name: form.name.value,
                        email: form.email.value,
                        phone: form.phone.value,
                        passwordPrevious: form.passwordPrevious.value,
                        password: form.password.value
                    }),
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.loadSession().jwt);
                    },
                    success: function (response) {

                        FlashMessage.show([
                            ['success', 'Suas informações foram editadas com sucesso']
                        ]);

                        let accountInfoModal = $('#account-info-modal');
                        accountInfoModal.modal('hide');

                    },
                    error: function (xhr, textStatus) {

                        FlashMessage.show([
                            ['danger', xhr.responseJSON.error]
                        ]);

                    },
                    complete: function (xhr, textStatus) {
                        console.log(xhr.status);
                    }
                });

            }
        });
    },


};

$(document).ready(function () {
    AppHome.init();
    ContactForms.init();
});