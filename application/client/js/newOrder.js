// The client only collects data and shows results. The server calculates the price.
const form = document.getElementById('orderForm');
const messageBox = document.getElementById('message');
const resultBox = document.getElementById('result');

function toggleQuantity(event) {
    const code = event.target.dataset.code;
    document.getElementById('qty_' + code).disabled = !event.target.checked;
}

function getCheckedCheckboxes() {
    return Array.from(document.querySelectorAll('input[type=checkbox]:checked'));
}

function hasMissingFields() {
    return ['orderId', 'fullName', 'address', 'email', 'phone']
        .some(id => document.getElementById(id).value.trim() === '');
}

function buildFormData() {
    const formData = new FormData(form);
    getCheckedCheckboxes().forEach(box => {
        const code = box.dataset.code;
        formData.append('products[' + code + ']', document.getElementById('qty_' + code).value);
    });
    return formData;
}

function showServerErrorPage(html) {
    document.open();
    document.write(html);
    document.close();
}

async function handleResponse(response) {
    const text = await response.text();
    if (response.ok) {
        resultBox.textContent = 'Order saved. Total price (including 21% VAT): ' + text + ' €';
        form.reset();
        document.querySelectorAll('input[type=number]').forEach(i => i.disabled = true);
    } else if (response.status === 404) {
        showServerErrorPage(text);
    } else {
        messageBox.textContent = 'Error: ' + text;
    }
}

async function sendOrder(event) {
    event.preventDefault();
    messageBox.textContent = '';
    resultBox.textContent = '';
    if (hasMissingFields() || getCheckedCheckboxes().length === 0) {
        messageBox.textContent = 'Please fill in all fields and select at least one product.';
        return;
    }
    try {
        const response = await fetch('../server/createOrder.php', { method: 'POST', body: buildFormData() });
        await handleResponse(response);
    } catch (error) {
        messageBox.textContent = 'Could not contact the server.';
    }
}

document.querySelectorAll('input[type=checkbox]').forEach(b => b.addEventListener('change', toggleQuantity));
form.addEventListener('submit', sendOrder);
