var FlashMessage = {
    show: function ($arrayMessages, containerName) {
        containerName = containerName || '.alert-container';
        let alertContainer = $(containerName);

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