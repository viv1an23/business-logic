var dateParam = getUrlParameter('reports_filter%5Bdate%5D') ?? null;

$(document).ready(function () {
    let startDate;
    let endDate;
    if (dateParam) {
        var newDate = dateParam.split('+-+');
        startDate = newDate[0];
        endDate = newDate[1];
    } else {
        startDate = moment().subtract(1, 'month');
        endDate = moment();
    }
    $('#reports_filter_date').daterangepicker({
        alwaysShowCalendars: true,
        locale: {
            format: 'YYYY-MM-DD'
        },
        startDate: startDate,
        endDate: endDate,
        ranges: {
            'Today': [moment(), moment()],
            'Week': [moment().startOf('week'), moment().endOf('week')],
            'Month': [moment().startOf('month'), moment().endOf('month')],
            'Year': [moment().startOf('year'), moment().endOf('year')],
        }
    }).on('apply.daterangepicker', function (e, picker) {
        var startDate = moment(picker.startDate, 'YYYY-MM-DD');
        var endDate = moment(picker.endDate, 'YYYY-MM-DD');
        let dayDiff = endDate.add(1,'day').diff(startDate, 'day');
        let monthDiff = endDate.diff(startDate, 'month');
        console.log(dayDiff)
        console.log(monthDiff)
        if (dayDiff >= 14 && monthDiff < 2) {
            $("#reports_filter_grouping").val('weekly');
            $("#reports_filter_grouping").trigger('change');
        } else if (monthDiff >= 2) {
            $("#reports_filter_grouping").val('monthly');
            $("#reports_filter_grouping").trigger('change');
        }
    });

});