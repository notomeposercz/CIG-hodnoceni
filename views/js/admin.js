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
    $(document).ready(function() {
  $('.rating-filter').change(function() {
    var rating = $(this).val();
    console.log("Filtruji: " + rating);
    
    if (rating === 'all') {
      $('.stat-row').show();
    } else {
      $('.stat-row').hide();
      $('.stat-row[data-rating="' + rating + '"]').show();
    }
  });
});