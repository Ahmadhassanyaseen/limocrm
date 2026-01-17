
function createpassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("ri-eye-off-line");
        icon.classList.add("ri-eye-line");
    } else {
        input.type = "password";
        icon.classList.remove("ri-eye-line");
        icon.classList.add("ri-eye-off-line");
    }
}
