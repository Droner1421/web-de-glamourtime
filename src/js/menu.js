function alternarMenu() {
    const enlacesNav = document.querySelector('.enlaces-nav');
    const botonAlternar = document.querySelector('.alternar-menu');
    enlacesNav.classList.toggle('active');
    botonAlternar.classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.item-nav').forEach(item => {
        item.addEventListener('click', () => {
            const enlacesNav = document.querySelector('.enlaces-nav');
            const botonAlternar = document.querySelector('.alternar-menu');
            if (enlacesNav.classList.contains('active')) {
                enlacesNav.classList.remove('active');
                botonAlternar.classList.remove('active');
            }
        });
    });
});