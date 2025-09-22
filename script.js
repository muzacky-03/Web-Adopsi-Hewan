// Fungsi untuk menangani pengajuan adopsi
document.querySelectorAll('.btn-adopt').forEach(button => {
    button.addEventListener('click', function(e) {
        if (!confirm('Apakah Anda yakin ingin mengajukan adopsi untuk kucing ini?')) {
            e.preventDefault();
        }
    });
});

// Validasi form rehome
document.querySelector('.rehome-form form')?.addEventListener('submit', function(e) {
    const agreement = this.querySelector('[type="checkbox"]');
    if (!agreement.checked) {
        alert('Anda harus menyetujui pernyataan sebelum melanjutkan');
        e.preventDefault();
    }
});

// Responsive navigation
const navToggle = document.createElement('button');
navToggle.className = 'nav-toggle';
navToggle.innerHTML = '☰';
navToggle.addEventListener('click', () => {
    document.querySelector('nav ul').classList.toggle('show');
});

document.querySelector('nav').prepend(navToggle);

// Media query for responsive design
function handleResize() {
    const navUl = document.querySelector('nav ul');
    if (window.innerWidth > 768) {
        navUl.classList.remove('show');
    }
}

window.addEventListener('resize', handleResize);