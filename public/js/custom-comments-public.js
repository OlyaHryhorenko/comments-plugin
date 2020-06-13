var comment_submit = document.querySelector('.custom-comments .custom-comment-submit');
var inputs = document.querySelector('.custom-comments ').getElementsByTagName('input');
var textarea = document.getElementById('custom_comments_text');
var form = document.querySelector('.custom-comments ');
comment_submit.addEventListener('click', function (e) {
	e.preventDefault();
	var data = document.querySelector('form.custom-comments');
	var _this = this;
	add_comment(data, document.querySelector('form.custom-comments'));
});
function add_comment(data, _this) {
	var name = data.elements['name'].value,
	 	email = data.elements['email'].value,
		text = data.elements['text'].value,
		type = data.elements['type'].value,
		id = data.elements['id'].value,
		captcha = '';
		_this = $(_this).closest('.custom-comments');
	if (typeof captcha_token !== 'undefined') {
		captcha = '&g-recaptcha-response='+captcha_token;
	}

	var pattern = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
	var pattern_name = /^[а-яА-Яa-zA-Z ]+$/;

	if ($(form).find('input[name="name"]').attr('required') != undefined) {
		if (name.length < 1 && pattern_name.test(name) === false  || name.length > 200) {
			showError(custom_comments.error_name, _this);
			return false;
		}
	}

	if ($(form).find('input[name="email"]').attr('required') != undefined) {
		if (pattern.test(email) === false || email.length < 1) {
			showError(custom_comments.error_email, _this);
			return false;
		}
	}

	if ($(form).find('textarea[name="text"]').attr('required') != undefined) {
		if (text.length < 1) {
			showError(custom_comments.error_text, _this);
			return false;
		}
	}


	var params = 'id=' + id + '&name=' + name + '&email='+email + '&text=' + text + '&type=' + type + captcha;
	var xhr = new XMLHttpRequest();
	var action = 'add_comment';
	var url = '/wp-admin/admin-ajax.php';

	xhr.open('POST', url + '?action=' + action);
	xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

	xhr.onload = function () {

		if (xhr.status === 200) {
			for (index = 0; index < inputs.length; ++index) {
				if (inputs[index].type == 'text' || inputs[index].type == 'email' || inputs[index].type == 'name') {
					inputs[index].value = '';
				}
			}
			textarea.value = '';
			showSuccess(_this);
			return false;
		}
	};
	xhr.send(params);
}


var showError = function (text, _this) {
	$('.end-message').remove();
	if (!($(_this).find(".form-p").length)) {
		$(_this).find(".custom-comment-submit").before("<p class='form-p' id='form-p'>" + text + "</p>");
	} else {
		$(_this).find(".form-p").text(text);
	}
}



var showSuccess = function (_this) {
	$('.end-message').remove();
	$('.form-p').remove();
	$(_this).find(".custom-comment-submit").before("<p class='end-message'>" + custom_comments.success_text + "</p>");
}