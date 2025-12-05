
document.addEventListener("DOMContentLoaded", () => {
    const cookieBanner = document.getElementById("cookie-consent");
    const acceptButton = document.getElementById("cookie-accept");

    if (localStorage.getItem("cookieConsent") === "true") {
        cookieBanner.style.display = "none";
    }

    acceptButton.addEventListener("click", () => {
        localStorage.setItem("cookieConsent", "true");
        cookieBanner.style.display = "none";
    });
});
