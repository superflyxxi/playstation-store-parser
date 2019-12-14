function hideAllClasses(arg1) {
	const all = document.querySelectorAll(arg1);
	all.forEach(element => {
		element.classList.add('hidden');
	});
}

function showAllClasses(arg1) {
	const all = document.querySelectorAll(arg1);
	all.forEach(element => {
		element.classList.remove('hidden');
	});
}
