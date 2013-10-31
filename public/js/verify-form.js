/**
 * Author: Leonardo Rocha
 */

function validateEmail()
{
    var emailField = document.getElementById('email-field').value;

    var emailRegex = /[a-zA-Z0-9]+([\.\-_][a-zA-Z0-9]*)*@[a-zA-Z0-9]+(\.[a-zA-Z]+)+/;
    var validEmail = emailRegex.test(emailField);
    
    if (!validEmail) {
        alert('E-mail inválido!');
        document.getElementById('email-field').focus();
    }

    return validEmail;
}

function validate(field)
{
    var fieldContent = document.getElementById(field).value;

    var regex = /^[a-zA-Záàâãéèêẽóôõñçú]+/;
    var valid = regex.test(fieldContent);

    if (!valid) document.getElementById(field).focus();

    return valid;
}

function verifyUserRegistration()
{
    var validEmail = validateEmail();

    var password = document.getElementById('password-field').value;
    var passwordConfirmation = document.getElementById('confirm-pass-field').value;

    var equalPasswords = (password == passwordConfirmation);

    var validName = validate('name-field');
    
    return (validEmail && equalPasswords && validName);
}

function verifyContactForm()
{
    var validEmail = validateEmail();
    var validName = validate('name-field');
    var validMessage = validate('message');

    return (validEmail && validName && validMessage);
}
