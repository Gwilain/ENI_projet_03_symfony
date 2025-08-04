document.querySelectorAll('.dropdown-toggle').forEach(btn => {
  btn.addEventListener('click', function () {
    const parent = this.closest('.dropdown-multi');
    parent.classList.toggle('open');
  });
});

// Clique en dehors → ferme tous les dropdowns
document.addEventListener('click', function (e) {
  document.querySelectorAll('.dropdown-multi').forEach(drop => {
    if (!drop.contains(e.target)) {
      drop.classList.remove('open');
    }
  });
});