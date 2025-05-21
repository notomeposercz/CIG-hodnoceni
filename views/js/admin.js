/**
 * Admin JavaScript pro modul Mezistranka hodnocení
 */
$(document).ready(function() {
    console.log('Admin JS pro hodnocení inicializován');
    
    // Export statistik do CSV
    $('#export-stats-btn').click(function(e) {
        if ($(this).attr('href')) {
            window.location.href = $(this).attr('href');
        }
    });
    
    // Filtrování podle hodnocení
    $('.rating-filter').on('change', function() {
        console.log('Filtruji podle hodnocení:', $(this).val());
        var rating = $(this).val();
        
        if (rating === 'all') {
            // Zobrazit všechny řádky
            $('.stat-row').show();
        } else {
            // Skrýt všechny řádky
            $('.stat-row').hide();
            // Zobrazit pouze řádky s požadovaným hodnocením
            $('.stat-row[data-rating="' + rating + '"]').show();
        }
    });
});