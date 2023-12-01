// script.js

// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function() {
    // Select an element to display the message (e.g., a div with an ID of "message")
    var messageElement = document.getElementById("message");

    // Check if the message element exists
    if (messageElement) {
        // Set the message content
        messageElement.innerHTML = "Script loaded successfully!";
    }
});
