/* ------------------------------------------------------------------------------
 *
 *  # Custom JS code
 *
 *  Place here all your custom js. Make sure it's loaded after app.js
 *
 * ---------------------------------------------------------------------------- */
(function () {
    let searchNavbarForm = document.querySelector("#navbar_search form");
    let cancelSearch = document.querySelector("#cancelSearch");
    searchNavbarForm.addEventListener('submit', function (e) {
        let fetchStatus;
        e.preventDefault();
        let searchUrl = "/cp/search";
        let searchText = document.querySelector("#searchInput").value;
            fetch(searchUrl, {
                method: 'POST',
                body: JSON.stringify({
                    searchText: searchText
                })
            }).then(function (response) {
                fetchStatus = response.status;
                return response.json();
            }).then(function (json) {
                if (json.status) {
                    let resultDiv = document.getElementById('searchResult');
                    let newElement = document.createElement('div');
                    newElement.id = "searchResults";
                    newElement.innerHTML += json.content;
                    resultDiv.innerHTML = "";
                    resultDiv.append(newElement);
                    document.getElementById('searchContent').classList.add('show');
                }
            }).catch(function (error) {
                console.log(error);
            });
    });
    cancelSearch.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector("#searchInput").value = "";
        let resultDiv = document.getElementById('searchResult');
        resultDiv.innerHTML = "";
        document.getElementById('searchContent').classList.remove('show');
    });
    document.addEventListener('click', function (e) {
        let searchContent = document.getElementById('navbar_search');
        let isClickedInsideContent = searchContent.contains(e.target);
        if (!isClickedInsideContent) {
            document.querySelector("#searchInput").value = "";
            let resultDiv = document.getElementById('searchResult');
            resultDiv.innerHTML = "";
            document.getElementById('searchContent').classList.remove('show');
        }
    });
    let primaryTheme = 'light';
    let secondaryTheme = 'dark';
    let htmlTag = document.querySelector('html');
    const themeMode = htmlTag.getAttribute('data-color-theme') != null ? htmlTag.getAttribute('data-color-theme') : 'light';

    let sortingElementFirst = document.createElement('i');
    sortingElementFirst.innerHTML = '&#x2191';

    let sortingElementSecond = document.createElement('i');
    sortingElementSecond.innerHTML = '&#x2193';

    let descElement = document.querySelector('table thead tr th a.desc');
    let ascElement = document.querySelector('table thead tr th a.asc');
    let sortableElement = document.querySelectorAll('table thead tr th a.sortable');
    if (sortableElement !== null) {
        let sortableElementFirst = document.createElement('i');
        sortableElementFirst.classList.add('text-muted');
        sortableElementFirst.innerHTML = '&#x2191&#x2193';
        sortableElement.forEach((a) => {
            a.appendChild(sortableElementFirst.cloneNode(true));
        });
    }
    if (themeMode === secondaryTheme) {
        if (descElement !== null) {
            sortingElementFirst.classList.add('text-muted');
            sortingElementSecond.classList.add('text-white');

            descElement.append(sortingElementFirst, sortingElementSecond);
        }
        if (ascElement !== null) {
            sortingElementFirst.classList.add('text-white');
            sortingElementSecond.classList.add('text-muted');

            ascElement.append(sortingElementFirst, sortingElementSecond);
        }
    } else if (themeMode === primaryTheme) {
        if (descElement !== null) {
            sortingElementFirst.classList.add('text-muted');
            sortingElementSecond.classList.add('text-dark');

            descElement.append(sortingElementFirst, sortingElementSecond);
        }
        if (ascElement !== null) {
            sortingElementFirst.classList.add('text-dark');
            sortingElementSecond.classList.add('text-muted');

            ascElement.append(sortingElementFirst, sortingElementSecond);
        }
    }

    let dateRangeField = document.querySelectorAll('.date_range_reset');
    dateRangeField.forEach((d) => {
        if (d !== null) {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                let id = d.getAttribute('data-bs-id');
                document.getElementById(id).value = "";
            });
        }
    });
})();

function getUrlParameter(sParam)
{
    let sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
}

function number_format(number, decimals, dec_point, thousands_sep)
{
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    let n = !isFinite(+number) ? 0 : +number,
        precision = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            let k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (precision ? toFixedFix(n, precision) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < precision) {
        s[1] = s[1] || '';
        s[1] += new Array(precision - s[1].length + 1).join('0');
    }
    return s.join(dec);
}