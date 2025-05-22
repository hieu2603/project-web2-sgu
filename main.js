const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirmPassword');
const checkboxPassword = document.getElementById('checkboxPassword');

checkboxPassword.onclick = function () {
  if (checkboxPassword.checked) {
    password.type = 'text';
    confirmPassword.type = 'text';
  } else {
    password.type = 'password';
    confirmPassword.type = 'password';
  }
}