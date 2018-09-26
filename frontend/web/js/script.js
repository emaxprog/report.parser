google.charts.load('current', {'packages': ['corechart']});

function drawChart(data) {
    let dataTable = new google.visualization.DataTable();
    dataTable.addColumn('number', 'Time');
    dataTable.addColumn('number', 'Profit');
    dataTable.addColumn({type: 'string', role: 'tooltip'});
    dataTable.addRows(data);
    let options = {
        hAxis: {
            title: 'Time'
        },
        vAxis: {
            title: 'Profit'
        },
        tooltip: {isHtml: true},
        animation: {
            duration: 1000,
            easing: 'out'
        }
    };

    let chart = new google.visualization.AreaChart(document.getElementById('chart'));
    chart.draw(dataTable, options);
}

function clearAlerts() {
    $('.alert').remove();
}

$(document).ready(function () {
    $submitBtn = $('form .btn');
    $fileInput = $('input:file');
    $fileInputContainer = $('form .form-group');
    $chartContainer = $('.chart__container');
    $('form').submit(function (e) {
        e.preventDefault();
        clearAlerts();
        if ($fileInput.val() == '') {
            return false;
        }
        $submitBtn.button('loading');
        let form = $(this),
            formAction = form.attr('action'),
            formData = new FormData(form[0]),
            formMethod = 'POST';
        $.ajax({
            type: formMethod,
            url: formAction,
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                let data = [];
                $.each(result, function (index, ticket) {
                    let shortText = `
                    Ticket : ${ticket['Profit']}
                    Type : ${ticket['Type']}
                    Profit : ${ticket['Profit']}
                    `;
                    let text = `
                    Ticket : ${ticket['Profit']}
                    Open Time : ${ticket['Open Time']}
                    Type : ${ticket['Type']}
                    Size : ${ticket['Size']}
                    Item : ${ticket['Item']}
                    Price : ${ticket['Price']}
                    S / L : ${ticket['S / L']}
                    T / P : ${ticket['T / P']}
                    Close Time : ${ticket['Close Time']}
                    Commission : ${ticket['Commission']}
                    Taxes : ${ticket['Taxes']}
                    Swap : ${ticket['Swap']}
                    Profit : ${ticket['Profit']}
                    `;
                    let tooltipText = ticket['Type'] == 'buy' ? text : shortText;
                    data[index] = [index, +ticket['Profit'], tooltipText];
                });
                $submitBtn.button('reset');
                $chartContainer.show();
                google.charts.setOnLoadCallback(drawChart(data));
            },
            error: function (msg) {
                let errors = msg.responseJSON.errors.reportFile;
                if (errors.length) {
                    $.each(errors, function (index, value) {
                        $('form .file-input').after(`<div class="alert alert-danger" role="alert">${value}</div>`);
                    });
                }
                $submitBtn.button('reset');
            }
        });
        return false;
    });

});