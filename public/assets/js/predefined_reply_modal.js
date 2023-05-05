document.getElementById("insertPredefinedReply").addEventListener("click", function (e) {
    e.preventDefault();
    categoriesPopup();
})
var categories_modal = new bootstrap.Modal(document.getElementById('categoriesModal'), {});

function categoriesPopup()
{
    fetch(path, {
        type: 'GET'
    }).then(function (response) {
        return response.json();
    }).then(function (json) {
        if (json.success) {
            let data = json.categories;
            let html = setupCategories(data);
            document.getElementById("searchReply").value;
            document.getElementById("response_allCategories").innerHTML = '';
            document.getElementById("category_replies").innerHTML = '';
            document.getElementById("response_allCategories").innerHTML = html;
            categories_modal.show();
        }
    })
}

function getReplies(id)
{
    path = replyPath.replace("__ID__", id);

    var categories = document.querySelectorAll(".categories");
    categories.forEach((a) => {
        if (a.classList.contains("text-info")) {
            a.classList.remove("text-info");
        }
    });

    document.getElementById("category_" + id).classList.add('text-info');
    fetch(path, {
        type: 'GET'
    }).then(function (response) {
        return response.json();
    }).then(function (json) {
        if (json.success) {
            let data = json.categories;
            let html = setupCategories(data);
            let repliesHtml = setupReplies(json.replies);
            let breadcrumbHtml = setupBreadcrumb(json.breadcrumbStructure.split(','));

            document.getElementById("searchReply").value;
            document.getElementById("category_replies").innerHTML = '';
            document.getElementById("response_allCategories").innerHTML = '';
            document.getElementById("category_replies").innerHTML = repliesHtml;
            document.getElementById("response_allCategories").innerHTML = html;
            document.getElementById("breadcrumb").innerHTML = breadcrumbHtml;
            document.getElementById("category_replies").classList.add('show')
        }
    });
}

function setupCategories(data)
{
    let html = '<div class="row"><div class="col-md-12 bg-light" id="breadcrumb"> <a href="javascript:void(0)" class="breadcrumb-item text-primary text-capitalize" onclick="categoriesPopup()"> Root </a> </div><h6 class="col-md-12 modal-title mt-2">Categories</h6>';
    if (data.length === 0) {
        html += '<div class="col-md-12 mt-3"> no records found </div>';
    } else {
        for (const category in data) {
            html += ' <div class="col-md-5 mt-1" id="categories">';
            html += ' <a href="javascript:void(0)" onclick="getReplies(' + data[category].id + ')" id="category_' + data[category].id + '"class="categories">';
            html += '<i class="ph-folder-open pr-1"></i>';
            html +=  data[category].name + ' (' + data[category].replies + ' replies | ' + data[category].subCategories + ' Subcategories)</a>';
            html += '</div>';
        }
    }
    html += '</div>';
    return html;
}

function setupReplies(data)
{
    let html = '<div class="row mt-3"><h6 class="col-md-12 modal-title" id="replies_section">Replies</h6>';
    if (data.length === 0) {
        html += '<div class="col-md-12 mt-1"> no records found </div>';
    } else {
        for (const reply in data) {
            html += '<div class="col-md-12 mt-1 cursor-pointer"><p onclick="setReply(' + data[reply].id + ')"> <i class="ph-pencil-simple pr-1"></i>';
            let textLength = data[reply].text.length;
            let text = data[reply].text;
            if (textLength > 80) {
                text = text.slice(0, 80) + '....';
            }
            html += data[reply].title + ' | ' + text;
            html += '</p><input type="hidden" id="reply_message_' + data[reply].id + '" value="' + data[reply].text + '"> </div>';
        }
    }
    html += "</div>";
    return html;
}

function setupBreadcrumb(breadcrumbStructure)
{
    let lastElementId = breadcrumbStructure[breadcrumbStructure.length - 1].split('-')[0];

    let html = [];
    for (let i = 0; i < breadcrumbStructure.length; i++ ) {
        let id = breadcrumbStructure[i].split('-')[0];
        let name = breadcrumbStructure[i].split('-')[1];
        if (id !== lastElementId) {
            html.push('<a href="javascript:void(0)" onclick="getReplies(' + id + ')" id="category_' + id + '"class="breadcrumb-item text-primary text-capitalize">' + name + '</a> ');
        } else {
            html.push('<a href="javascript:void(0)" id="category_' + id + '"class="breadcrumb-item text-capitalize">' + name + '</a> ')
        }
    }
    return html.join('');
}

function setReply(id)
{
    var text = document.getElementById("reply_message_" + id).value;
    document.querySelector("#add_reply_reply, #support_ticket_message").value = text;
    document.getElementById("category_replies").classList.remove('show');
    categories_modal.hide();
}

var delayInAjaxCall = (function () {
    var timer = 0;
    return function (callback, milliseconds) {
        clearTimeout(timer);
        timer = setTimeout(callback, milliseconds);
    };
})();

function searchReply()
{
    let searchText = document.getElementById("searchReply").value;
    var l = Ladda.create(document.querySelector('#cancelSearchSp'));

    if (searchText !== '' && searchText !== null) {
        delayInAjaxCall(function () {

            var formData = new FormData();
            formData.append('searchText', document.getElementById("searchReply").value);
            fetch(searchReplyPath, {
                method: 'POST',
                body: formData
            }).then(function (response) {
                return response.json();
            }).then(function (json) {
                if (json.success) {
                    let repliesHtml = setupReplies(json.replies, true);
                    document.getElementById("category_replies").innerHTML = '';
                    document.getElementById("response_allCategories").innerHTML = '';
                    setTimeout(function () {
                        document.getElementById("category_replies").innerHTML = repliesHtml;
                        document.getElementById("replies_section").innerHTML = "Search Result";
                        document.getElementById("category_replies").classList.add("show");
                        document.getElementById("cancelSearchSp").removeAttribute('disabled');
                        l.stop();
                    }, 500);
                }
            });
        }, 1200);
    } else {
        document.getElementById("category_replies").innerHTML = '';
        categoriesPopup();
    }
}

document.getElementById("cancelSearchSp").addEventListener("click", function () {
    document.getElementById("searchReply").value = "";
    cancelSearch();
});

function cancelSearch()
{
    var searchReply = document.getElementById("searchReply").value;
    if (searchReply !== '') {
        categoriesPopup();
        document.getElementById("category_replies").innerHTML = '';
    }
}