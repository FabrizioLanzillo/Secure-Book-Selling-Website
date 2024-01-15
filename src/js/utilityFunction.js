function checkPasswordStrength(buttonId) {
    var password = document.getElementById("password").value;
    var result = zxcvbn(password);

    // Update password strength meter
    document.getElementById("password-strength-meter").value = result.score;

    // Update password strength text
    let text = "";
    var suggestion = "";
    switch (result.score) {
        case 0:
            text = "Very Weak";
            break;
        case 1:
            text = "Weak";
            break;
        case 2:
            text = "Moderate";
            break;
        case 3:
            text = "Strong";
            break;
        case 4:
            text = "Very Strong";
            break;
        default:
            break;
    }

    // Protect against DOM based XSS
    document.getElementById("password-strength-text").textContent = text;
}

function validateForm() {
    const birthdateInput = document.getElementsByName('birthdate')[0];
    const birthdate = new Date(birthdateInput.value);

    const eighteenYearsAgo = new Date();
    eighteenYearsAgo.setFullYear(eighteenYearsAgo.getFullYear() - 18);

    if (birthdate > eighteenYearsAgo) {
        // Mostra l'errore e impedisce l'invio del form
        alert("You must be at least 18 years old to sign up.");
        birthdateInput.classList.add('error'); // Aggiungi la classe CSS per evidenziare l'errore
        return false; // Impedisce l'invio del form
    }

    // Rimuovi l'eventuale evidenziazione precedente
    birthdateInput.classList.remove('error');
    return true;
}
