<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // aplicar modo oscuro si esta guardado en localStorage
    if (localStorage.getItem('darkMode') === 'true') {
        activarModoOscuro(true);
    }

    // evento para el boton de modo oscuro
    $('#darkModeToggle').click(function() {
        const modoActual = $('body').hasClass('dark-mode');
        activarModoOscuro(!modoActual);
    });

    function activarModoOscuro(activar) {
        $('body').toggleClass('dark-mode', activar);
        $('.sidebar').toggleClass('dark-mode', activar);
        localStorage.setItem('darkMode', activar);

        if (activar) {
            $('body').css('background-color', '#1a1a1a');
        } else {
            $('body').css('background-color', '#f8f9fa');
        }
    }
});
</script>
