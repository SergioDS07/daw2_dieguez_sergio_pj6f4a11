function checkOrderCode(event) {
  if (document.getElementById("orderId").value.trim() === "") {
    event.preventDefault();
    document.getElementById("message").textContent =
      "Please enter an order code.";
  }
}

document
  .getElementById("searchForm")
  .addEventListener("submit", checkOrderCode);
