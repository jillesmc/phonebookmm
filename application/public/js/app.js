var AppHome = {
    s: {
        loader: $('#loader'),
        topBarUserName: $('#top-bar .user-name'),

        buttonLogout: $('#logout'),
        inputSearch: $('#inputSearch'),
        buttonSearchCleanner: $('.search__clear-button'),
        tableContacts: $('table.contacts-table'),
        tableContactsBody: $('table.contacts-table tbody'),

        modalContactAdd: $('#add-contact-modal'),
        modalContactView: $('#view-contact-modal'),
        modalContactDelete: $('#delete-contact-modal'),
        modalContactEdit: $('#edit-contact-modal'),
        modalAccountInfo: $('#account-info-modal'),
        buttonModalContactEdit: $('#view-contact-modal__edit-button'),
        buttonModalContactDelete: $('#view-contact-modal__remove-button'),

        buttonDelete: $('#deleteContactButton'),

        formContactAdd: $('#addContactForm'),
        formContactEdit: $('#editContactForm'),
        formAccountInfo: $('#accountInfoForm'),

        buttonAddPhones: $('.form-contact__btn--add-more-phones'),
        buttonDeletePhone: $('.phone--remove'),
        inputPhones: $('input[name="inputPhone[]"]'),

        inputAccountInfoPhone: $('#accountInputPhone'),
    },

    init: function () {
        AppHome.initPlugins();
        AppHome.bindUiActions();
        AppHome.bindFormValidations();
        AppHome.initUiElements();

        AppHome.loadUserData(); //ajax
        AppHome.loadContactsList(); //ajax
    },

    initPlugins: function () {
        //Tabela com cabecalho fixo
        AppHome.s.tableContacts.floatThead({top: 67});
    },

    initUiElements: function () {
        AppHome.maskPhone();
        AppHome.maskPhone(AppHome.s.inputAccountInfoPhone);
    },

    bindUiActions: function () {
        // Quando clica no logout
        AppHome.s.buttonLogout.click(function () {
            sessionStorage.clear();
            window.location.href = '/'
        });

        // Quando digita no campo de busca
        let timeOut = null;
        AppHome.s.inputSearch.keyup(function () {
            let input = this;
            clearTimeout(timeOut);
            timeOut = setTimeout(function () {
                AppHome.s.buttonSearchCleanner.toggle(Boolean($(input).val()));
                AppHome.searchContacts($(input).val());
            }, 500);
        });

        // Quando clica para limpar a busca
        AppHome.s.buttonSearchCleanner.click(function () {
            AppHome.s.inputSearch.val('').focus();
            AppHome.searchContacts($(this).val());
            $(this).hide();
        });

        // Quando clica no editar do modal de ve
        AppHome.s.buttonModalContactEdit.click(function () {
            AppHome.s.modalContactView.removeClass('fade');
            AppHome.s.modalContactEdit.removeClass('fade');
            AppHome.s.modalContactView.modal('hide');
            AppHome.s.modalContactEdit.modal('show');
            AppHome.s.modalContactView.addClass('fade');
            AppHome.s.modalContactEdit.addClass('fade');

            AppHome.loadContactToEditForm();
        });

        // Quando clica no deletar do modal de ver
        AppHome.s.buttonModalContactDelete.click(function () {
            AppHome.s.modalContactView.removeClass('fade');
            AppHome.s.modalContactDelete.removeClass('fade');
            AppHome.s.modalContactView.modal('hide');
            AppHome.s.modalContactDelete.modal('show');
            AppHome.s.modalContactView.addClass('fade');
            AppHome.s.modalContactDelete.addClass('fade');

            AppHome.loadContactToModalContent(AppHome.s.modalContactDelete);
        });

        // Quando exibe o modal de ver
        AppHome.s.modalContactView.on('show.bs.modal', function (event) {
            let modal = $(event.target);
            let dataContainer;
            if (event.relatedTarget !== undefined) {
                dataContainer = $(event.relatedTarget.closest('tr'));
                sessionStorage.setItem('contactId', dataContainer.data('id'));
            }

            AppHome.loadContactToModalContent(modal);
        });

        // Quando exibe o modal de editar
        AppHome.s.modalContactEdit.on('show.bs.modal', function (event) {
            if (event.relatedTarget !== undefined) {
                dataContainer = $(event.relatedTarget.closest('tr'));
                sessionStorage.setItem('contactId', dataContainer.data('id'));
            }

            AppHome.loadContactToEditForm();
        });

        // Quando exibe o modal de deletar
        AppHome.s.modalContactDelete.on('show.bs.modal', function (event) {
            let modal = $('#delete-contact-modal');
            if (event.relatedTarget !== undefined) {
                dataContainer = $(event.relatedTarget.closest('tr'));
                sessionStorage.setItem('contactId', dataContainer.data('id'));
            }
            AppHome.loadContactToModalContent(modal);
        });

        // Quando exibe o modal de editar info da conta
        AppHome.s.modalAccountInfo.on('show.bs.modal', function (event) {
            AppHome.loadUserToAccountInfoForm();
        });

        // Quando oculta o modal de editar
        AppHome.s.modalContactEdit.on('hide.bs.modal', function (e) {
            AppHome.clearForms();
        });

        // Quando oculta o modal de adicionar
        AppHome.s.modalContactAdd.on('hide.bs.modal', function (e) {
            AppHome.clearForms();
        });

        // Quando oculta o modal de deletar
        AppHome.s.modalContactDelete.on('hide.bs.modal', function (e) {
            AppHome.clearDeleteContactData();
        });

        // Adicionar novos campos de telefone dinamicamente
        AppHome.s.buttonAddPhones.click(function (event) {
            event.preventDefault();
            let removeHtml = '<button type="button" class="phone--remove btn btn-light"><span class="oi oi-x' +
                ' text-danger"></span></button>';
            let removeButton = $(removeHtml);
            let container = $(this).closest('.row').prev();
            let nextChildIdNumber = container.children().length;
            let clonedPhoneField = container.children().first().clone();
            let inputPhoneField = clonedPhoneField.find('input');

            //prepara novo campo de telefone
            inputPhoneField.attr('name', 'inputPhone[' + nextChildIdNumber + ']');
            inputPhoneField.removeAttr('aria-invalid');
            inputPhoneField.removeAttr('aria-describedby');
            inputPhoneField.val('');
            inputPhoneField.removeClass('is-invalid');
            inputPhoneField.next('small').remove();
            inputPhoneField.parent().prev().remove();

            AppHome.maskPhone(inputPhoneField);

            clonedPhoneField.find('.input-group').append(removeButton);
            container.append(clonedPhoneField);

            inputPhoneField.rules('add',
                {
                    minlength: 12,
                    messages: {
                        minlength: "Telefone incompleto"
                    }
                });

            AppHome.attachButtonDeletePhone();
            inputPhoneField.focus();

            AppHome.s.formContactAdd.validate();
        });


        // Quando clica no botão deletar final
        AppHome.s.buttonDelete.click(function (event) {
            AppHome.processContactDelete();
        });

    },

    bindFormValidations: function () {

        // Validação formulário que adiciona contato
        AppHome.s.formContactAdd.validate({
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
                AppHome.processFormContactAdd(form);
            }
        });

        // Validação formulário que edita contato
        AppHome.s.formContactEdit.validate({
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
                AppHome.processFormContactEdit(form)
            }
        });


        // Validação formulário que edita conta
        AppHome.s.formAccountInfo.validate({
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
                AppHome.processFormAccountInfo(form);
            }
        });
    },

    // AJAX carrega a lista de contatos
    loadContactsList: function () {
        let user_id = AppHome.getSessionData().user.id;
        AppHome.s.loader.show();
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/api/users/" + user_id + "/contacts",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                AppHome.s.loader.hide();

                AppHome.buildContactTable(response.data);
            },
            error: function (xhr, textStatus) {
                AppHome.s.loader.hide();
                switch (xhr.status) {
                    case 404:
                        FlashMessage.show([
                            ['success', 'Sua lista de contatos está vazia. Vamos começar']
                        ]);
                        AppHome.buildContactTable([]);
                        break;
                    default:
                        FlashMessage.show([
                            ['error', xhr.responseJSON.message]
                        ]);
                        break;

                }

            }
        });

    },

    // AJAX carrega a lista de contatos com base na pesquisa
    searchContacts: function (query) {
        let userId = AppHome.getSessionData().user.id;

        AppHome.s.tableContactsBody.html('');
        AppHome.s.loader.show();

        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            data: {'q': query},
            url: "/api/users/" + userId + "/contacts",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                AppHome.s.loader.hide();
                AppHome.buildContactTable(response.data);
            },
            error: function (xhr, textStatus) {
                AppHome.s.loader.hide();
                switch (xhr.status) {
                    case 404:
                        FlashMessage.show([
                            ['success', 'Nenhum contato encontrado']
                        ]);
                        AppHome.buildContactTable([]);
                        break;
                    default:
                        FlashMessage.show([
                            ['error', xhr.responseJSON.message]
                        ]);
                        break;

                }

            }
        });
    },

    // AJAX carrega os dados o usuario
    loadUserData: function () {

        $.ajax({
            type: 'GET',
            contentType: "application/json",
            url: "/api/users/" + AppHome.getSessionData().user.id,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                let userName = response.data.name.split(' ')[0]; //firstname
                AppHome.s.topBarUserName.html(userName);
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

    // AJAX carrega os dados do contato para os modals de ver ou deletar
    loadContactToModalContent: function (modal) {
        let userId = AppHome.getSessionData().user.id;
        let contactId = sessionStorage.getItem('contactId');
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/api/users/" + userId + "/contacts/" + contactId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                let phonesNotesHtml = '';

                phonesNotesHtml += '<div class="mb-4">' +
                    '<h5>' + response.data.name + '</h5>' +
                    '</div>';

                if (response.data.email) {
                    phonesNotesHtml += '<p>' +
                        '<div class="float-left pr-3 text-right" ><span class="oi oi-envelope-closed' +
                        ' text-muted"></span></div>' +
                        '<a href="mailto:' + response.data.email + '" class="">' + response.data.email + '</a>' +
                        '</p>';
                }

                for (let i = 0; i < response.data.phones.length; i++) {
                    let numberOnlyPhone = response.data.phones[i].phone.replace(' ', '').replace('-', '');
                    phonesNotesHtml += '<p>' +
                        '<div class="float-left pr-3 text-right" ><span class="oi oi-phone' +
                        ' text-muted"></span></div>' +
                        '<a href="tel:' + numberOnlyPhone + '">' +
                        response.data.phones[i].phone +
                        '</a>' +
                        '</p>';
                }

                if (response.data.note) {
                    phonesNotesHtml += '<p>' +
                        '<div class="float-left pr-3 text-right"><span class="oi oi-book' +
                        ' text-muted"></span></div>' +
                        response.data.note +
                        '</p>';
                }

                modal.find('.view-contact-data').html(phonesNotesHtml);
            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    default:
                        FlashMessage.show([
                            ['error', xhr.responseJSON.message]
                        ]);
                        break;
                }
            }
        });
    },

    // AJAX carrega os dados do contato para o formulário de editar
    loadContactToEditForm: function () {
        let contactId = sessionStorage.getItem('contactId');
        let userId = AppHome.getSessionData().user.id;
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/api/users/" + userId + "/contacts/" + contactId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                AppHome.s.formContactEdit.find('input[name="name"]').val(response.data.name);
                AppHome.s.formContactEdit.find('input[name="email"]').val(response.data.email);
                AppHome.s.formContactEdit.find('textarea[name="note"]').val(response.data.note);

                for (let i = 0; i < response.data.phones.length; i++) {
                    let index = i !== 0 ? i : '';

                    if (AppHome.s.formContactEdit.find('input[name="inputPhone[' + index + ']"]').length === 0) {
                        AppHome.s.buttonAddPhones.click();
                    }

                    AppHome.s.formContactEdit.find('input[name="inputPhone[' + index + ']"]').val(response.data.phones[i].phone);

                }
            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    default:
                        FlashMessage.show([
                            ['error', xhr.responseJSON.message]
                        ]);
                        break;
                }
            }
        });
    },

    // AJAX Carrega os dados do usuário para o formulário de informações da conta
    loadUserToAccountInfoForm: function () {
        let userId = AppHome.getSessionData().user.id;
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/api/users/" + userId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                AppHome.s.formAccountInfo.find('input[name="name"]').val(response.data.name);
                AppHome.s.formAccountInfo.find('input[name="email"]').val(response.data.email);
                AppHome.s.formAccountInfo.find('input[name="phone"]').val(response.data.phone);

                AppHome.s.formAccountInfo.find('input[name="password"]').val('');
                AppHome.s.formAccountInfo.find('input[name="passwordPrevious"]').val('');
                AppHome.s.formAccountInfo.find('input[name="passwordConfirm"]').val('');

                AppHome.s.formAccountInfo.find('input.is-invalid').each(function () {
                    $(this).removeAttr('aria-invalid');
                    $(this).removeAttr('aria-describedby');
                    $(this).removeClass('is-invalid');
                    $(this).removeClass('is-invalid');
                    $(this).next('small').remove();
                });
            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    default:
                        FlashMessage.show([
                            ['error', xhr.responseJSON.message]
                        ]);
                        break;
                }
            }
        });
    },


// AJAX adiciona contato
    processFormContactAdd: function (form) {
        let sessionData = AppHome.getSessionData();
        let userId = sessionData.user.id;
        let phones = [];

        $(form).find('input[name^="inputPhone"]').each(function () {
            phones.push($(this).val());
        });

        $.ajax({
            type: 'POST',
            contentType: "application/json",
            url: "/api/users/" + userId + "/contacts",
            data: JSON.stringify({
                name: form.name.value,
                email: form.email.value,
                phones: phones,
                note: form.note.value
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                sessionStorage.setItem('contactId', response.data.contactId);

                FlashMessage.show([
                    ['success', 'Contato criado com sucesso']
                ]);

                AppHome.loadContactsList();
                AppHome.s.modalContactAdd.removeClass('fade');
                AppHome.s.modalContactView.removeClass('fade');
                AppHome.s.modalContactAdd.modal('hide');
                AppHome.s.modalContactView.modal('show');
                AppHome.s.modalContactAdd.addClass('fade');
                AppHome.s.modalContactView.addClass('fade');

            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    default:
                        FlashMessage.show([
                            ['danger', xhr.responseJSON.message]
                        ]);
                        break;
                }

            }
        });
    },

    // AJAX edita contato
    processFormContactEdit: function (form) {
        let sessionData = AppHome.getSessionData();
        let userId = sessionData.user.id;
        let contactId = sessionStorage.getItem('contactId');
        let phones = [];

        $(form).find('input[name^="inputPhone"]').each(function () {
            phones.push($(this).val());
        });

        $.ajax({
            type: 'PUT',
            contentType: "application/json",
            url: "/api/users/" + userId + "/contacts/" + contactId,
            data: JSON.stringify({
                name: form.name.value,
                email: form.email.value,
                phones: phones,
                note: form.note.value
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                sessionStorage.setItem('contactId', response.data.contactId);

                FlashMessage.show([
                    ['success', 'Contato editado com sucesso']
                ]);

                AppHome.loadContactsList();
                AppHome.s.modalContactEdit.removeClass('fade');
                AppHome.s.modalContactView.removeClass('fade');
                AppHome.s.modalContactEdit.modal('hide');
                AppHome.s.modalContactView.modal('show');
                AppHome.s.modalContactEdit.addClass('fade');
                AppHome.s.modalContactView.addClass('fade');

            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    default:
                        FlashMessage.show([
                            ['danger', xhr.responseJSON.message]
                        ]);
                        break;
                }

            }
        });

    },

    // AJAX edita informações da conta
    processFormAccountInfo: function (form) {
        let sessionData = AppHome.getSessionData();
        let userId = sessionData.user.id;

        $.ajax({
            type: 'PUT',
            contentType: "application/json",
            url: "/api/users/" + userId,
            data: JSON.stringify({
                name: form.name.value,
                email: form.email.value,
                phone: form.phone.value,
                passwordPrevious: form.passwordPrevious.value,
                password: form.password.value
            }),
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {

                FlashMessage.show([
                    ['success', 'Suas informações foram editadas com sucesso']
                ]);

                AppHome.s.modalAccountInfo.modal('hide');

            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    default:
                        FlashMessage.show([
                            ['danger', xhr.responseJSON.message]
                        ]);
                        break;
                }
            }
        });
    },
    // AJAX deleta contato da lista
    processContactDelete: function () {
        let sessionData = AppHome.getSessionData();
        let userId = sessionData.user.id;
        let contactId = sessionStorage.getItem('contactId');

        $.ajax({
            type: 'DELETE',
            contentType: "application/json",
            dataType: "json",
            url: "/api/users/" + userId + "/contacts/" + contactId,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + AppHome.getSessionData().jwt);
            },
            success: function (response) {
                AppHome.s.modalContactDelete.modal('hide');
                AppHome.loadContactsList();
                FlashMessage.show([
                    ['success', 'Contato removido com sucesso']
                ])
            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    default:
                        FlashMessage.show([
                            ['error', xhr.responseJSON.message]
                        ]);
                        break;
                }
            }
        });
    },


    // constroi a tabela de contatos com base um uma lista de dados
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

        AppHome.s.tableContactsBody.html(contactRowHtml);
    },

    // limpa os dados que estavam no modal de deletar
    clearDeleteContactData: function () {
        let loadingHtml = '<div class="text-center">' +
            '<img src="/images/loading-spinner.svg" alt="" width="100">' +
            '</div>'
        AppHome.s.modalContactDelete.find('.view-contact-data').html(loadingHtml);
    },

    // limpa os formulários de editar e adicionar contatos
    clearForms: function () {
        [AppHome.s.formContactEdit, AppHome.s.formContactAdd].forEach(function (formElement) {
            $(formElement).find('.phone--remove').click();
            $(formElement)[0].reset();

            $(formElement).find('input.is-invalid').each(function () {
                $(this).removeAttr('aria-invalid');
                $(this).removeAttr('aria-describedby');
                $(this).removeClass('is-invalid');
                $(this).removeClass('is-invalid');
                $(this).next('small').remove();
            });
        });
    },

    // adiciona botão de deletar aos campos de telefone dinâmicos
    attachButtonDeletePhone: function () {
        // é necessário chamar o seletor aqui para que ele pegue os campos dinâmicos
        let deletePhoneButtons = $('.phone--remove');
        deletePhoneButtons.off();
        deletePhoneButtons.click(function () {
            $(this).closest('.row').remove();
        });
    },

    // mascara de telefone
    maskPhone: function (field) {
        let phoneInput = field || AppHome.s.inputPhones;
        phoneInput.mask('00 0000-00000', {
            onKeyPress: function (phoneNumber, e, field, options) {
                let masks = ['00 0000-00000', '00 00000-0000'];
                let mask = (phoneNumber.length > 12) ? masks[1] : masks[0];
                field.mask(mask, options);
            }
        });
    },

    // carrega dados da sessão que estão no local storage
    getSessionData: function () {
        let sessionData;
        sessionData = JSON.parse(sessionStorage.getItem('user_session'));
        if (!sessionData || !sessionData.user || !sessionData.user.id) {
            window.location.href = '/';
        }
        return sessionData;
    }
};


$(document).ready(function () {
    AppHome.init();
});