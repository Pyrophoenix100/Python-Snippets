/**
 * This is a HTML include library, entirely in JS. You can utilize it by creating an
 * <import src="[path to html file]"> tag and importing the library. Nothing else needs to be done.
 * This library includes no safety features and is very likely horribly unsafe. This is only intended for 
 * static sites and homebrew projects. That being said, if you can make money off this, I encourage it. 
 *
 * @example
 * <h1> Above header </h1>
 * <import src="header.html">
 * <h1> Underneath header </h1>
 *
 */


function HTMLFileBody(fileUrl) {
    return new Promise((resolve, reject) => {
      fetch(fileUrl)
        .then(response => response.text())
        .then(htmlString => {
          let parser = new DOMParser();
          let htmlDoc = parser.parseFromString(htmlString, 'text/html');
          let bodyTag = htmlDoc.getElementsByTagName('*');
          resolve(bodyTag);
        })
        .catch(error => {
          reject(error);
        });
    });
  }
  let includes = document.getElementsByTagName("include");
  includesArray = Array.from(includes);
  includesArray.forEach((element) => {
      HTMLFileBody(element.attributes['src'].value)
      .then(collection => {
          element.innerHTML = collection[0].innerHTML;
      })
  });