function checkPasswordStrength() {
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
            document.getElementById("suggestions").innerHTML = "";
            break;
        default:
            break;
    }

    if (result.score < 3){
        if (result.feedback.suggestions.length > 0) {
            // Mostra all'utente i suggerimenti per migliorare la password
            result.feedback.suggestions.forEach(function(suggestion) {
                suggestion = ("Advice: " + suggestion);
                document.getElementById("suggestions").innerHTML = suggestion;
            });
        }
    }
    document.getElementById("password-strength-text").innerHTML = text;
}