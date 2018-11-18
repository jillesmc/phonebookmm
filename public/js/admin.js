var Admin = {
    init: function () {
        Admin.newUsersChart();
        Admin.newContactsChart();
        Admin.usersDDDChart();
        Admin.contactsDDDChart();
    },
    newUsersChart: function () {
        let subscriptionData = {
            labels: [
                '01/11', '02/11', '03/11', '04/11', '05/11',
                '06/11', '07/11', '08/11', '09/11', '10/11',
                '11/11', '12/11', '13/11', '14/11', '15/11'
            ],
            datasets: [{
                label: "# de registros",
                data: [
                    0, 59, 75, 20, 20, 55, 40,
                    0, 5, 6, 0, 20,
                    0, 4, 6, 0, 1
                ],
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
    newContactsChart: function () {
        let insertionData = {
            labels: [
                '01/11', '02/11', '03/11', '04/11', '05/11',
                '06/11', '07/11', '08/11', '09/11', '10/11',
                '11/11', '12/11', '13/11', '14/11', '15/11'
            ],
            datasets: [{
                label: "# de registros",
                data: [
                    0, 59, 75, 20, 20, 55, 40,
                    0, 5, 6, 0, 20,
                    0, 4, 6, 0, 1
                ],
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
    usersDDDChart: function () {
        let usersDDDData = {
            labels: [
                11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22, 24, 27, 28, 31, 32, 33, 34, 35, 37, 38, 41, 42, 43, 44, 45,
                46, 47, 48, 49, 51, 53, 54, 55, 61, 62, 63, 64, 65, 66, 67, 68, 69, 71, 73, 74, 75, 77, 79, 81, 82, 83,
                84, 85, 86, 87, 88, 89, 91, 92, 93, 94, 95, 96, 97, 98, 99
            ],
            datasets: [{
                label: "# de registros",
                data: [
                    99, 98, 97, 96, 95, 94, 93, 92, 91, 89, 88, 87, 86, 85, 84, 83, 82, 81, 79, 77, 75, 74, 73, 71, 69,
                    68, 67, 66, 65, 64, 63, 62, 61, 55, 54, 53, 51, 49, 48, 47, 46, 45, 44, 43, 42, 41, 38, 37, 35, 34,
                    33, 32, 31, 28, 27, 24, 22, 21, 19, 18, 17, 16, 15, 14, 13, 12, 11

                ],
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
    contactsDDDChart: function () {
        let contactsDDDData = {
            labels: [
                11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22, 24, 27, 28, 31, 32, 33, 34, 35, 37, 38, 41, 42, 43, 44, 45,
                46, 47, 48, 49, 51, 53, 54, 55, 61, 62, 63, 64, 65, 66, 67, 68, 69, 71, 73, 74, 75, 77, 79, 81, 82, 83,
                84, 85, 86, 87, 88, 89, 91, 92, 93, 94, 95, 96, 97, 98, 99
            ],
            datasets: [{
                label: "# de registros",
                data: [
                    99, 98, 97, 96, 95, 94, 93, 92, 91, 89, 88, 87, 86, 85, 84, 83, 82, 81, 79, 77, 75, 74, 73, 71, 69,
                    68, 67, 66, 65, 64, 63, 62, 61, 55, 54, 53, 51, 49, 48, 47, 46, 45, 44, 43, 42, 41, 38, 37, 35, 34,
                    33, 32, 31, 28, 27, 24, 22, 21, 19, 18, 17, 16, 15, 14, 13, 12, 11

                ],
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