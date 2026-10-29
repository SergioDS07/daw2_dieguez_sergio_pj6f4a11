// Shared by the menus: goes to the page chosen in the select.
function goToSelectedOperation() {
    window.location.href = document.getElementById('operation').value;
}

document.getElementById('goButton').addEventListener('click', goToSelectedOperation);
