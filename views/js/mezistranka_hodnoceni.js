/**
 * Hodnotící modul pro PrestaShop - optimalizovaná verze
 */

// Globální funkce pro inicializaci modulu - volaná z patičky při dynamickém vložení
function initializeRatingModule() {
  console.log("Manuální inicializace hodnotícího modulu");
  initializeRatingComponent();
}

// Hlavní inicializační funkce - volaná automaticky nebo manuálně
function initializeRatingComponent() {
  try {
    console.log("Mezistranka hodnoceni - inicializace");

    // Kontrola existence prvků
    const starContainer = document.getElementById("starContainer");
    if (!starContainer) {
      console.log("Element s ID 'starContainer' nebyl nalezen - přeskakuji inicializaci.");
      return;
    }

    const stars = starContainer.querySelectorAll("span");
    if (stars.length === 0) {
      console.error("Žádné hvězdičky (span) nebyly nalezeny v #starContainer!");
      return;
    }

    const positive = document.getElementById("positive");
    const negative = document.getElementById("negative");

    // Proměnná pro uložení aktuálního hodnocení
    let currentRating = 0;
    
    // Skryjeme nabídky na začátku
    if (positive) positive.style.display = "none";
    if (negative) negative.style.display = "none";
  
    // Přidáme kliknutí na každou hvězdičku a cursor pointer
    stars.forEach((star, index) => {
      star.style.cursor = "pointer";
      
      star.addEventListener("click", function() {
        const rating = index + 1;
        console.log("Kliknuto na hvězdičku: " + rating);
        
        // Uložení aktuálního hodnocení
        currentRating = rating;
        
        // Zbarvení hvězdiček
        stars.forEach(s => s.style.color = "#ccc");
        for (let i = 0; i < rating; i++) {
          if (stars[i]) stars[i].style.color = "gold";
        }
        
        // Zobrazení příslušných odkazů podle počtu hvězdiček
        if (positive) positive.style.display = rating >= 4 ? "block" : "none";
        if (negative) negative.style.display = rating < 4 ? "block" : "none";
        
        // Přidáme tracking na odkazy po zobrazení
        if (rating >= 4 && positive) {
          attachTrackingToLinks(positive.querySelectorAll("a"), 'Pozitivni', rating);
        } else if (rating < 4 && negative) {
          attachTrackingToLinks(negative.querySelectorAll("a"), 'Negativni', rating);
        }
      });
    });
    
    // Funkce pro přidání trackingu na odkazy
    function attachTrackingToLinks(links, linkType, rating) {
      links.forEach(link => {
        if (!link.getAttribute('data-tracked')) {
          link.addEventListener('click', function(e) {
            const linkText = this.textContent.trim();
            console.log("Kliknuto na " + linkType + " odkaz: " + linkText);
            
            // Odesíláme data o hodnocení a kliknutí na odkaz v jednom požadavku
            trackRatingAndClick(rating, linkType + ' - ' + linkText);
          });
          link.setAttribute('data-tracked', 'true');
        }
      });
    }
    
    // Funkce pro odeslání dat o hodnocení a kliknutí
    function trackRatingAndClick(rating, linkLabel) {
      // Pokud existuje GA, použijeme i ten
      if (typeof gtag !== 'undefined') {
        gtag('event', 'rating_conversion', {
          'event_category': 'Hodnoceni',
          'event_label': linkLabel,
          'value': rating
        });
      }
      
      // Odešleme data na náš server
      sendTrackingData('rating_conversion', {
        category: 'Hodnoceni',
        label: linkLabel,
        value: rating
      });
    }
    
    // Funkce pro odeslání dat na server
    function sendTrackingData(eventName, eventData) {
      try {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/modules/mezistranka_hodnoceni/track.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        
        const payload = {
          event: eventName,
          data: eventData,
          timestamp: new Date().toISOString(),
          page: window.location.pathname,
          userAgent: navigator.userAgent
        };
        
        xhr.send(JSON.stringify(payload));
        console.log('Trackována událost:', eventName, eventData);
      } catch (e) {
        console.error('Chyba při trackování události:', e);
      }
    }
    
    console.log("Mezistranka hodnoceni - inicializace dokončena");
  } catch (e) {
    console.error("Chyba při inicializaci hodnocení:", e);
  }
}

// Inicializace po načtení DOMu - okamžitá inicializace bez zbytečného zpoždění
document.addEventListener("DOMContentLoaded", initializeRatingComponent);