<?php
// Konfiguration
$recipient = "techcode@rasiel-moser.com";

// Nur POST zulassen
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit();
}

// Hilfsfunktion für sichere Ausgabe
function esc($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// POST-Daten (nur noch die echten Felder)
$name      = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$message   = trim($_POST['Nachricht'] ?? '');

// Basisinfos
$receivedAt = date("d.m.Y H:i");
$senderIp   = $_SERVER['REMOTE_ADDR'] ?? 'unbekannt';
$userAgent  = $_SERVER['HTTP_USER_AGENT'] ?? 'unbekannt';


// reCAPTCHA prüfen
$recaptchaSecret   = "6LdxLAgsAAAAAJAe-4ihLYjmiLzzD5KFoVWxpBdZ";
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
$captchaValid      = false;

if (!empty($recaptchaResponse)) {
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($recaptchaSecret) . "&response=" . urlencode($recaptchaResponse);
    $response = @file_get_contents($verifyUrl);

    if ($response !== false) {
        $responseKeys = json_decode($response, true);
        if (!empty($responseKeys['success']) && $responseKeys['success'] === true) {
            $captchaValid = true;
        }
    }
}

// Pflichtfelder prüfen
$emailValid        = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
$hasRequiredFields = $name !== '' && $emailValid && $message !== '';
$errorMessage      = '';

// Fehlermeldung setzen, falls nötig
if (!$captchaValid) {
    $errorMessage = 'Please solve the captcha correctly!';
} elseif (!$hasRequiredFields) {
    $errorMessage = 'Please fill out all mandatory fields correctly!';
}

// Nur senden, wenn kein Fehler aufgetreten ist
if ($errorMessage === '') {
    $subject = "Nachricht von $name";
    $subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";

    $htmlMessage = "<html><body>";
    $htmlMessage .= "<p><strong>Name:</strong> " . esc($name) . "</p>";
    $htmlMessage .= "<p><strong>Email:</strong> " . esc($email) . "</p>";
    $htmlMessage .= "<p><strong>Nachricht:</strong><br>" . nl2br(esc($message)) . "</p>";
    $htmlMessage .= "<hr><p style='font-size:12px;color:#666;'>Empfangen am: $receivedAt<br>IP: " . esc($senderIp) . "<br>User-Agent: " . esc($userAgent) . "</p>";
    $htmlMessage .= "</body></html>";

    $headers = "From: Kontaktformular <techcode@rasiel-moser.com>\r\n";
    $headers .= "Reply-To: " . esc($email) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";


    // Mail senden und nur bei Erfolg weiterleiten
    if (mail($recipient, $subject, $htmlMessage, $headers)) {
        header('Location: thanks.html');
        exit();
    } else {
        $errorMessage = 'Die Nachricht konnte nicht gesendet werden. Bitte versuchen Sie es später.';
    }
}
?>

<?php if($errorMessage): ?>
<div class="error-overlay">
    <div class="error-popup">
        <div class="error-icon">⚠️</div>
        <h2>Error sending</h2>
        <p><?php echo esc($errorMessage); ?></p>
        <a href="contact.html" class="error-btn">Back to the form</a>
    </div>
</div>

<style>
/* Vollbild-Abdeckung */
.error-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: green;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* Popup-Box */
.error-popup {
    background: linear-gradient(135deg, #fdecea, #fce8e6);
    padding: 50px 60px;
    max-width: 600px;
    width: 90%;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 12px 35px rgba(0,0,0,0.35);
    animation: popupIn 0.5s ease forwards;
}

/* Icon */
.error-icon {
    font-size: 7rem;
    color: #f44336;
    margin-bottom: 20px;
    animation: bounce 1s infinite;
}

/* Überschrift */
.error-popup h2 {
    margin: 0 0 20px;
    color: #b71c1c;
    font-size: 2.5rem;
}

/* Text */
.error-popup p {
    margin-bottom: 35px;
    color: #333;
    font-size: 1.5rem;
    line-height: 1.6;
}

/* Button */
.error-btn {
    display: inline-block;
    padding: 18px 40px;
    background-color: #22b622;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.25);
    transition: all 0.3s ease;
}
.error-btn:hover {
    background-color: #1ea01e;
    transform: translateY(-2px);
}

/* Animations */
@keyframes popupIn {
    0% { opacity: 0; transform: scale(0.3); }
    60% { opacity: 1; transform: scale(1.05); }
    80% { transform: scale(0.98); }
    100% { transform: scale(1); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

/* Responsive & Größenanpassung */
@media screen and (max-width: 1024px) {
    .error-popup { padding: 50px 45px; max-width: 90%; }
    .error-icon { font-size: 7.5rem; }
    .error-popup h2 { font-size: 2.7rem; }
    .error-popup p { font-size: 1.6rem; }
    .error-btn { font-size: 1.6rem; padding: 20px 45px; }
}

@media screen and (max-width: 768px) {
    .error-popup { padding: 50px 40px; max-width: 95%; }
    .error-icon { font-size: 8rem; }
    .error-popup h2 { font-size: 3rem; }
    .error-popup p { font-size: 1.7rem; }
    .error-btn { font-size: 1.7rem; padding: 22px 50px; }
}

@media screen and (max-width: 480px) {
    .error-popup { padding: 45px 35px; max-width: 98%; }
    .error-icon { font-size: 9rem; }
    .error-popup h2 { font-size: 3.2rem; }
    .error-popup p { font-size: 1.8rem; }
    .error-btn { font-size: 1.8rem; padding: 24px 55px; }
}



</style>
<?php endif; ?>