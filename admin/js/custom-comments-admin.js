document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('.repeater-add-btn')) {
        document.querySelector('.repeater-add-btn').addEventListener('click', function (e) {
            e.preventDefault();
            var row = document.querySelector('.repeater tr:last-child');
            var new_row = row.cloneNode(true);
            inputs = new_row.querySelectorAll('input');
            Array.prototype.forEach.call(inputs, function (item, index) {
                number = item.dataset.number;
                new_number = parseInt(number) + 1;
                item.dataset.number = parseInt(item.dataset.number) + 1;
                item.name = item.name.replace(number, new_number);
                item.value = '';
            });
            document.querySelector('.repeater').appendChild(new_row);
        })

    }
    var quick_edit = document.querySelectorAll('.quick-edit a');
    Array.prototype.forEach.call(quick_edit, function (item, index) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            var container = this.parentNode.parentNode.parentNode.parentNode;
            var author = container.querySelector('.c_comment_author').innerText;
            var email = container.querySelector('.c_comment_email').innerText;
            var comment = container.querySelector('.c_comment_content').innerText;
            var ID = container.querySelector('.c_comment_ID').value;
            var type = container.querySelector('.column-type').innerText;
            this.parentNode.parentNode.parentNode.parentNode.innerHTML = '';
            var td = document.createElement('td');
            td.colSpan = 8;
            td.classList = 'colspanchange';
            container.appendChild(td);
            var fieldset = document.createElement('fieldset');
            td.appendChild(fieldset);
            create_node(author, fieldset, 'input', 'text', 'form-control author-value', 'Author');
            create_node(email, fieldset, 'input', 'email', 'form-control email-value', 'Email');
            create_node(comment, fieldset, 'textarea', 'text', 'form-control-textarea content-value', 'Comment');
            create_node(ID, fieldset, 'input', 'hidden', 'form-control content-ID', '');
            create_node(type, fieldset, 'input', 'hidden', 'form-control content-type', '');

            var node = document.createElement('input');
            node.type = "submit";
            node.value = 'Edit';
            node.classList = 'button button-primary quick_edit_submit';
            fieldset.appendChild(node);


        });

    });

    $('body').on('click', '.quick_edit_submit', function (e) {
        e.preventDefault();
        var container = this.parentNode;
        var params = {
            'ID': container.querySelector('.content-ID input').value,
            'type': container.querySelector('.content-type input').value,
            'author': container.querySelector('.author-value input').value,
            'email': container.querySelector('.email-value input').value,
            'content': container.querySelector('.content-value textarea').value,
        };
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                params: params,
                action: 'quick_edit',
            },
            success: function (data) {
                var data = JSON.parse(data);
                alert("Response status: " + data.status);
                location.reload();
            }
        });
    });

    $('body').on('click', '.remove-btn', function (e) {
        e.preventDefault();
        if (document.querySelectorAll('.remove-btn').length > 1) {
            this.parentNode.parentNode.remove();
        }
    });

    function create_node(value, container, el, type, classes, label_text) {
        var label = document.createElement('label');
        var newContent = document.createTextNode(label_text);
        label.classList = classes;
        if (type != 'hidden') {
            label.appendChild(newContent);
        }
        var node = document.createElement(el);
        node.type = type;
        //node.classList = classes;
        node.value = value;
        label.appendChild(node);
        container.appendChild(label)
    }

});