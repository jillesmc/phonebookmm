var Admin = {
    init: function () {
        Admin.bindActions();
        Admin.getUserData();
        Admin.getDashboarData();
        Admin.newUsersChart();
        Admin.newContactsChart();
        Admin.usersDDDChart();
        Admin.contactsDDDChart();
    },
    bindActions: function(){
      $('#logout').click(function () {
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
            url: "/admin/users/" + sessionData.admin.id,
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
    getDashboarData: function(){
        $.ajax({
            type: 'GET',
            contentType: "application/json",
            dataType: "json",
            url: "/admin/data",
            beforeSend: function (xhr) {
                xhr.setRequestHeader('Authorization', 'BEARER ' + Admin.loadSession().jwt);
            },
            success: function (response) {

                let labels = [];
                let data = [];

                $('#users_total').html(response.data.users.total);
                $('#contacts_total').html(response.data.contacts.total);
                $('#users_total_last_month').html(response.data.users_last_month.total);
                $('#contacts_total_last_month').html(response.data.contacts_last_month.total);



                for(let i = 0; i < response.data.users_last_fifteen_days_per_day.length; i++){
                    labels.push(response.data.users_last_fifteen_days_per_day[i].day);
                    data.push(response.data.users_last_fifteen_days_per_day[i].total);
                }
                Admin.newUsersChart(labels, data);

                labels = [];
                data = [];
                for(let i = 0; i < response.data.contacts_last_fifteen_days_per_day.length; i++){
                    labels.push(response.data.contacts_last_fifteen_days_per_day[i].day);
                    data.push(response.data.contacts_last_fifteen_days_per_day[i].total);
                }
                Admin.newContactsChart(labels, data);

                labels = [];
                data = [];
                for(let i = 0; i < response.data.users_per_zone_code.length; i++){
                    labels.push(response.data.users_per_zone_code[i].zone_code);
                    data.push(response.data.users_per_zone_code[i].total);
                }
                Admin.usersDDDChart(labels, data);

                labels = [];
                data = [];
                for(let i = 0; i < response.data.contacts_per_zone_code.length; i++){
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

        let canvas = $('#newUsersFromPastFifteenDays');
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

        let canvas = $('#newContactsFromPastFifteenDays');
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

        let canvas = $('#usersDDD');
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

        let canvas = $('#contactsDDD');
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