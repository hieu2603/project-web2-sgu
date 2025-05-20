const password = document.getElementById('password');
const checkboxPassword = document.getElementById('checkboxPassword');

checkboxPassword.onclick = function () {
  if (checkboxPassword.checked) {
    password.type = 'text';
  } else {
    password.type = 'password';
  }
}