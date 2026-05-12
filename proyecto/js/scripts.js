$(function() {
    // Initialize dark mode from localStorage
    if (localStorage.getItem('darkMode') === 'true') {
        toggleDarkMode(true);
    }

    // Dark mode toggle handler
    $('#darkModeToggle').click(function() {
        toggleDarkMode(!$('body').hasClass('dark-mode'));
    });

    // Sidebar navigation with smooth animations
    $('.list-group-item').click(function(e) {
        e.preventDefault();
        
        // Update active nav item
        $('.list-group-item').removeClass('active');
        $(this).addClass('active');
        
        // Smooth content transition
        $('#content-area').animate({
            opacity: 0,
            marginLeft: '50px'
        }, 300, function() {
            // Content loading would happen here
            $(this).animate({
                opacity: 1,
                marginLeft: '0'
            }, 300);
        });
    });
});

function toggleDarkMode(enable) {
    $('body').toggleClass('dark-mode', enable);
    $('.sidebar').toggleClass('dark-mode', enable);
    localStorage.setItem('darkMode', enable);
    
    if (enable) {
        $('body').css('background-color', '#1a1a1a');
    } else {
        $('body').css('background-color', '#f8f9fa');
    }
}
