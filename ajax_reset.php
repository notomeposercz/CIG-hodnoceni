// Zachycení kliknutí na potvrzovací tlačítko v modálním okně
$('#confirm-reset-btn').click(function(e) {
    e.preventDefault();
    $.ajax({
        url: baseAdminDir + 'index.php',
        data: {
            controller: 'AdminModules',
            action: 'resetStats',
            ajax: 1,
            module_name: 'mezistranka_hodnoceni'
        },
        type: 'POST',
        success: function(response) {
            if (response.success) {
                // Reload stránky po úspěšném resetu
                window.location.reload();
            }
        }
    });
});