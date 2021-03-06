var Admin = {
    s: {
        buttonLogout: $('#logout'),
        containerUsersTotal: $('#users_total'),
        containerContactsTotal: $('#contacts_total'),
        containerUsersTotalLastMonth: $('#users_total_last_month'),
        containerContactsTotalLastMonth: $('#contacts_total_last_month'),

        containerGraphNewUsers: $('#newUsersFromPastFifteenDays'),
        containerGraphNewContacts: $('#newContactsFromPastFifteenDays'),
        containerGraphUsersDDD: $('#usersDDD'),
        containerGraphContactsDDD: $('#contactsDDD')
    },

    init: function () {
        Admin.bindActions();

        // AJAX load data
        Admin.getUserData();
        Admin.getDashboarData();

        // build graph
        Admin.newUsersChart();
        Admin.newContactsChart();
        Admin.usersDDDChart();
        Admin.contactsDDDChart();
    },
    bindActions: function () {
        Admin.s.buttonLogout.click(function () {
            sessionStorage.clear();
            window.location.href = '/admin'
        });
    },
    loadSession: function () {
        let sessionData;
        sessionData = JSON.parse(sessionStorage.getItem('admin_session'));
        if (!sessionData || !sessionData.admin || !sessionData.admin.id) {
            window.location.href = '/admin';
        }
        return sessionData;
    },
    getUserData: function () {
        let sessionData = Admin.loadSession();
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            url: "/api/admin/users/" + sessionData.admin.id,
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + Admin.loadSession().jwt);
            },
            error: function (xhr, textStatus) {
                switch (xhr.status) {
                    case 401:
                        FlashMessage.show([
                            ['danger', xhr.responseJSON.message]
                        ]);
                        setTimeout(function () {
                            window.location.href = '/admin'
                        }, 2000);
                        break;
                }

            }
        });

    },
    getDashboarData: function () {
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/api/admin/data",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + Admin.loadSession().jwt);
            },
            success: function (response) {

                let labels = [];
                let data = [];

                Admin.s.containerUsersTotal.html(response.data.users.total);
                Admin.s.containerContactsTotal.html(response.data.contacts.total);
                Admin.s.containerUsersTotalLastMonth.html(response.data.users_last_month.total);
                Admin.s.containerContactsTotalLastMonth.html(response.data.contacts_last_month.total);


                for (let i = 0; i < response.data.users_last_fifteen_days_per_day.length; i++) {
                    labels.push(response.data.users_last_fifteen_days_per_day[i].day);
                    data.push(response.data.users_last_fifteen_days_per_day[i].total);
                }
                Admin.newUsersChart(labels, data);

                labels = [];
                data = [];
                for (let i = 0; i < response.data.contacts_last_fifteen_days_per_day.length; i++) {
                    labels.push(response.data.contacts_last_fifteen_days_per_day[i].day);
                    data.push(response.data.contacts_last_fifteen_days_per_day[i].total);
                }
                Admin.newContactsChart(labels, data);

                labels = [];
                data = [];
                for (let i = 0; i < response.data.users_per_zone_code.length; i++) {
                    labels.push(response.data.users_per_zone_code[i].zone_code);
                    data.push(response.data.users_per_zone_code[i].total);
                }
                Admin.usersDDDChart(labels, data);

                labels = [];
                data = [];
                for (let i = 0; i < response.data.contacts_per_zone_code.length; i++) {
                    labels.push(response.data.contacts_per_zone_code[i].zone_code);
                    data.push(response.data.contacts_per_zone_code[i].total);
                }
                Admin.contactsDDDChart(labels, data);

            },
            error: function (xhr, textStatus) {
                FlashMessage.show([
                    ['danger', 'Algo não deu certo']
                ]);
            }
        });

    },
    newUsersChart: function (labels, data) {
        let subscriptionData = {
            labels: labels,
            datasets: [{
                label: "# de registros",
                data: data,
            }]
        };

        let chartOptions = {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 80,
                    fontColor: 'black'
                }
            }
        };

        let canvas = Admin.s.containerGraphNewUsers;
        let chart = new Chart(canvas, {
            type: 'line',
            data: subscriptionData,
            options: chartOptions
        });
    },
    newContactsChart: function (labels, data) {
        let insertionData = {
            labels: labels,
            datasets: [{
                label: "# de registros",
                data: data,
                backgroundColor: 'rgba(23, 168, 184, 0.5)',
            }]
        };

        let chartOptions = {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 80,
                    fontColor: 'black'
                }
            }
        };

        let canvas = Admin.s.containerGraphNewContacts;
        let chart = new Chart(canvas, {
            type: 'line',
            data: insertionData,
            options: chartOptions
        });
    },
    usersDDDChart: function (labels, data) {
        let usersDDDData = {
            labels: labels,
            datasets: [{
                label: "# de registros",
                data: data,
            }]
        };

        let chartOptions = {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 80,
                    fontColor: 'black'
                }
            }
        };

        let canvas = Admin.s.containerGraphUsersDDD;
        let chart = new Chart(canvas, {
            type: 'bar',
            data: usersDDDData,
            options: chartOptions
        });

    },
    contactsDDDChart: function (labels, data) {
        let contactsDDDData = {
            labels: labels,
            datasets: [{
                label: "# de registros",
                data: data,
                backgroundColor: 'rgba(23, 168, 184, 0.5)',
            }]
        };

        let chartOptions = {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 80,
                    fontColor: 'black'
                }
            }
        };

        let canvas = Admin.s.containerGraphContactsDDD;
        let chart = new Chart(canvas, {
            type: 'bar',
            data: contactsDDDData,
            options: chartOptions
        });
    }
};

$(document).ready(function () {
    Admin.init();
});