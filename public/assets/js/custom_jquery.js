$(document).ready(function () {
    if (!$().select2) {
        console.warn('Warning - select2.min.js is not loaded.');
        return;
    }
    $(".select").select2({
        minimumResultsForSearch: Infinity,
    });
    $(".select-search").select2();

    $(".single-date-range-picker").daterangepicker({
        parentEl: '.content-inner',
        singleDatePicker: true,
        timePicker: true,
        timePickerSeconds: true,
        timePicker24Hour: true,
        locale: {
            format: 'YYYY-MM-DD HH:mm:ss'
        }
    });

    $(".single-date-picker").daterangepicker({
        parentEl: '.content-inner',
        singleDatePicker: true,
        timePicker24Hour: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });
});

function currency_formatter(decimals, prefix, suffix, dec_point, thousands_sep)
{
    return {
        decimals: decimals,
        prefix: prefix,
        suffix: suffix,
        dec_point: dec_point,
        thousands_sep: thousands_sep,
        format: function (number) {
            return this.prefix + number_format(number, this.decimals, this.dec_point, this.thousands_sep) + this.suffix;
        }
    };
}