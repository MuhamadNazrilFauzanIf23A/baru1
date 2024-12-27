// Seleksi semua elemen card
const cards = document.querySelectorAll('.card');

// Tambahkan event listener untuk efek hover menggunakan JavaScript
cards.forEach((card) => {
    // Ketika mouse masuk (hover)
    card.addEventListener('mouseenter', () => {
        card.style.transition = '0.3s ease';
        card.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
        card.style.transform = 'scale(1.07)';
     });

    // Ketika mouse keluar
    card.addEventListener('mouseleave', () => {
        card.style.boxShadow = 'none';
        card.style.transform = 'scale(1)';
    });
});


