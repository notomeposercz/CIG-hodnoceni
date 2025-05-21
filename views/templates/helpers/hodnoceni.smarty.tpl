{* Šablona pro komponentu hodnocení *}
<div class="rating-container" style="max-width: 800px; margin: 40px auto; padding: 30px; text-align: center; background-color: #ffffff; border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);">
  <h2 style="margin-bottom: 25px; font-size: 28px; color: #333; font-weight: 600;">{$hodnoceni_title}</h2>
  <div id="starContainer" class="stars" style="font-size: 50px; margin: 30px 0; display: flex; justify-content: center; gap: 15px;">
    <span data-star="1" style="color: #ccc; cursor: pointer; transition: all 0.3s ease; display: inline-block;">★</span>
    <span data-star="2" style="color: #ccc; cursor: pointer; transition: all 0.3s ease; display: inline-block;">★</span>
    <span data-star="3" style="color: #ccc; cursor: pointer; transition: all 0.3s ease; display: inline-block;">★</span>
    <span data-star="4" style="color: #ccc; cursor: pointer; transition: all 0.3s ease; display: inline-block;">★</span>
    <span data-star="5" style="color: #ccc; cursor: pointer; transition: all 0.3s ease; display: inline-block;">★</span>
  </div>
  
  <div id="positive" style="display: none; margin-top: 30px; padding: 25px; border-radius: 10px; background-color: #f0f9f0; border-left: 5px solid #4CAF50;">
    <h3 style="margin-bottom: 15px; font-size: 22px; color: #333;">{$hodnoceni_positive_title}</h3>
    <p style="margin-bottom: 20px; font-size: 16px; color: #555; line-height: 1.5;">{$hodnoceni_positive_text}</p>
    <a href="{$hodnoceni_link_google}" target="_blank" rel="noreferrer noopener" style="display: inline-block; margin: 10px; padding: 12px 24px; color: #2E7D32; text-decoration: none; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-weight: 500; border: 1px solid #C8E6C9; background: #E8F5E9;">Google</a>
    <a href="{$hodnoceni_link_seznam}" target="_blank" rel="noreferrer noopener" style="display: inline-block; margin: 10px; padding: 12px 24px; color: #2E7D32; text-decoration: none; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-weight: 500; border: 1px solid #C8E6C9; background: #E8F5E9;">Seznam</a>
    <a href="{$hodnoceni_link_heureka}" target="_blank" rel="noreferrer noopener" style="display: inline-block; margin: 10px; padding: 12px 24px; color: #2E7D32; text-decoration: none; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-weight: 500; border: 1px solid #C8E6C9; background: #E8F5E9;">Heureka</a>
  </div>
  
  <div id="negative" style="display: none; margin-top: 30px; padding: 25px; border-radius: 10px; background-color: #fff8f8; border-left: 5px solid #ff9800;">
    <h3 style="margin-bottom: 15px; font-size: 22px; color: #333;">{$hodnoceni_negative_title}</h3>
    <p style="margin-bottom: 20px; font-size: 16px; color: #555; line-height: 1.5;">{$hodnoceni_negative_text}</p>
    <a href="tel:{$hodnoceni_phone|replace:' ':''}" style="display: block; width: 80%; margin: 10px auto; padding: 12px 24px; color: #D84315; text-decoration: none; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-weight: 500; border: 1px solid #FFCCBC; background: #FBE9E7; text-align: center;">Telefon: {$hodnoceni_phone}</a>
    <a href="mailto:{$hodnoceni_email}" style="display: block; width: 80%; margin: 10px auto; padding: 12px 24px; color: #D84315; text-decoration: none; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-weight: 500; border: 1px solid #FFCCBC; background: #FBE9E7; text-align: center;">E-mail: {$hodnoceni_email}</a>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  setTimeout(function() {
    var stars = document.querySelectorAll("#starContainer span");
    var positive = document.getElementById("positive");
    var negative = document.getElementById("negative");
    
    stars.forEach(function(star, index) {
      star.addEventListener("click", function() {
        var rating = index + 1;
        
        stars.forEach(function(s) { s.style.color = "#ccc"; });
        for (var i = 0; i < rating; i++) {
          if (stars[i]) stars[i].style.color = "gold";
        }
        
        if (positive) positive.style.display = rating >= 4 ? "block" : "none";
        if (negative) negative.style.display = rating < 4 ? "block" : "none";
        
        // Odeslání dat
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{$tracking_url}', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.send(JSON.stringify({
          event: 'rating_conversion',
          data: {
            category: 'Hodnoceni',
            value: rating
          }
        }));
      });
    });
    
    // Tracking kliknutí na odkazy
    if (positive) {
      positive.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
          var linkText = this.textContent.trim();
          
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '{$tracking_url}', true);
          xhr.setRequestHeader('Content-Type', 'application/json');
          xhr.send(JSON.stringify({
            event: 'rating_conversion',
            data: {
              category: 'Hodnoceni',
              label: 'Pozitivni - ' + linkText,
              value: 5
            }
          }));
        });
      });
    }
    
    if (negative) {
      negative.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
          var linkText = this.textContent.trim();
          
          var xhr = new XMLHttpRequest();
          xhr.open('POST', '{$tracking_url}', true);
          xhr.setRequestHeader('Content-Type', 'application/json');
          xhr.send(JSON.stringify({
            event: 'rating_conversion',
            data: {
              category: 'Hodnoceni',
              label: 'Negativni - ' + linkText,
              value: 2
            }
          }));
        });
      });
    }
  }, 500);
});
</script>