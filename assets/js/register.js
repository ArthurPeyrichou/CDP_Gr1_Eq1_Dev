const password = document.getElementById("registration_password_first");
const password_confirm = document.getElementById("registration_password_second");

function validatePassword(){
    if(password.value !== password_confirm.value) {
        password_confirm.setCustomValidity("Les mots de passe sont différents");
    } else {
        password_confirm.setCustomValidity('');
    }
}

password.onchange = validatePassword;
password_confirm.onkeyup = validatePassword;
