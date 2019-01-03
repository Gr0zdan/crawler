var divs = document.getElementsByTagName('div');
function changeColor(e) {
  e.target.style.color = 'red';
	e.target.remove();
}
for (var i = 0; i < divs.length; i++) {
	divs[i].addEventListener('click', changeColor);
}
