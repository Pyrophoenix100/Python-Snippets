a = document.getElementsByTagName("a");
b = Array.from(a);
b.forEach((c) => {
    c.outerHTML = "<span>" + c.innerHTML + "</span>";
    c.addEventListener('click', () => {this.style.textDecoration = 'strikethrough'});
    c.href = '';
});