// Client side only handles the user interface; authentication is done by the server.
const userInput = document.getElementById('user');
const passwordInput = document.getElementById('password');
const messageBox = document.getElementById('message');

function showMessage(text) {
    messageBox.textContent = text;
}

function hasMissingData() {
    return userInput.value.trim() === '' || passwordInput.value === '';
}

async function sendLogin() {
    const formData = new FormData();
    formData.append('user', userInput.value.trim());
    formData.append('password', passwordInput.value);
    try {
        const response = await fetch('../server/login.php', { method: 'POST', body: formData });
        handleLoginResponse(response);
    } catch (error) {
        showMessage('Could not contact the server.');
    }
}

function handleLoginResponse(response) {
    if (response.ok) {
        window.location.href = 'menu.html';
    } else {
        alert('Authentication error: the user could not be validated.');
        window.location.href = 'index.html';
    }
}

function onLoginClick() {
    if (hasMissingData()) {
        showMessage('Please enter both user name and password.');
        return;
    }
    showMessage('');
    sendLogin();
}

document.getElementById('loginButton').addEventListener('click', onLoginClick);
