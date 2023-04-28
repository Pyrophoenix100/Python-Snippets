/* Bits.js - Alec Tuchscherer - 2023 */
// Bits.js is a small library file for bits of js code that don't deserve their own file (yet)

//When called, makes all elements with the class 'typed' type out with the specified duration
function typingText(delay, leader="_") {
    const typedElements = document.getElementsByClassName("typed");
    for (let i = 0; i < typedElements.length; i++) {
        const text = typedElements[i].textContent;
        typedElements[i].textContent = "";
        typeText(typedElements[i], text, delay, leader);
    }
}

async function typeText(element, text, delay, leader) {
    for (let i = 0; i < text.length; i++) {
        element.textContent += text.charAt(i) + leader;
        await sleep(delay);
        element.textContent = element.textContent.slice(0, i+1)
    }
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}