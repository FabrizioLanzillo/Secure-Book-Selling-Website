  function formatExpirationDate(event) {
    // Get the input element
    const input = event.target;

    // Remove non-numeric characters
    const value = input.value.replace(/\D/g, '');

    // Format the date as MM/YY
    if (value.length >= 2) {
      const formattedValue = value.slice(0, 2) + '/' + value.slice(2, 4);
      input.value = formattedValue;
    } else {
      input.value = value;
    }
  }

  function formatCardNumber(event) {
    const input = event.target;
    let value = input.value.replace(/\s/g, ''); // Remove existing spaces
    value = value.replace(/(\d{4})/g, '$1 ');  // Add a space every 4 digits
    input.value = value.trim(); // Trim any leading/trailing spaces
}