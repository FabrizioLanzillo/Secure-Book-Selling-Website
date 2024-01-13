  // function formatExpirationDate(event) {
  //   // Get the input element
  //   const input = event.target;
  //
  //   // Remove non-numeric characters
  //   const value = input.value.replace(/\D/g, '');
  //
  //   // Format the date as MM/YY
  //   if (value.length >= 2) {
  //     const formattedValue = value.slice(0, 2) + '/' + value.slice(2, 4);
  //     input.value = formattedValue;
  //   } else {
  //     input.value = value;
  //   }
  //
  //   checkExpirationDate(input.value)
  // }
  //
  // function checkExpirationDate(expirationDate) {
  //     const today = new Date();
  //     const currentYear = today.getFullYear() % 100;
  //     const currentMonth = today.getMonth() + 1; // In JavaScript, months are zero-based, so we add 1.
  //
  //     const [inputMonth, inputYear] = expirationDate.split('/').map(Number);
  //
  //     if (
  //         inputYear < currentYear ||
  //         (inputYear === currentYear && (inputMonth < currentMonth || inputMonth < 1 || inputMonth > 12))
  //     ) {
  //         // Invalid expiration date (past date or invalid month)
  //         alert("La data di scadenza inserita è invalida. Inserisci una data futura valida.");
  //     }
  // }

  function formatExpirationDate(event) {
      const input = event.target;
      const value = input.value.replace(/\D/g, '');

      if (value.length >= 2) {
          const formattedValue = value.slice(0, 2) + '/' + value.slice(2, 4);
          input.value = formattedValue;
      } else {
          input.value = value;
      }

      checkExpirationDate(input.value);
  }

  function checkExpirationDate(expirationDate) {
      const today = new Date();
      const currentYear = today.getFullYear() % 100;
      const currentMonth = today.getMonth() + 1; // In JavaScript, months are zero-based, so we add 1.

      const [inputMonth, inputYear] = expirationDate.split('/').map(Number);

      if (
          inputYear < currentYear ||
          (inputYear === currentYear && (inputMonth < currentMonth || inputMonth < 1 || inputMonth > 12))
      ) {
          // Invalid expiration date (past date or invalid month)
          alert("La data di scadenza inserita è invalida. Inserisci una data futura valida.");
      }
  }

  function formatCardNumber(event) {
    const input = event.target;
    let value = input.value.replace(/\s/g, ''); // Remove existing spaces
    value = value.replace(/(\d{4})/g, '$1 ');  // Add a space every 4 digits
    input.value = value.trim(); // Trim any leading/trailing spaces
}