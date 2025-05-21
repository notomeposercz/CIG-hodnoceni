<div class="rating-container">
  <h2>{if isset($hodnoceni_title)}{$hodnoceni_title}{else}{l s='Jak jste spokojeni s našimi službami?' d='Modules.Mezistranka_hodnoceni.Rating'}{/if}</h2>
  <div id="starContainer" class="stars">
    <span data-star="1">★</span>
    <span data-star="2">★</span>
    <span data-star="3">★</span>
    <span data-star="4">★</span>
    <span data-star="5">★</span>
  </div>
  
  <div id="positive">
    <h3>{if isset($hodnoceni_positive_title)}{$hodnoceni_positive_title}{else}{l s='Děkujeme za vaše pozitivní hodnocení!' d='Modules.Mezistranka_hodnoceni.Rating'}{/if}</h3>
    <p>{if isset($hodnoceni_positive_text)}{$hodnoceni_positive_text}{else}{l s='Moc nás těší, že jste spokojeni s našimi službami. Pomohli byste nám sdílet vaši dobrou zkušenost i na jiných platformách?' d='Modules.Mezistranka_hodnoceni.Rating'}{/if}</p>
    <a href="{if isset($hodnoceni_link_google)}{$hodnoceni_link_google}{else}https://www.google.com{/if}" target="_blank" rel="noreferrer noopener">Google</a>
    <a href="{if isset($hodnoceni_link_seznam)}{$hodnoceni_link_seznam}{else}https://www.firmy.cz{/if}" target="_blank" rel="noreferrer noopener">Seznam</a>
    <a href="{if isset($hodnoceni_link_heureka)}{$hodnoceni_link_heureka}{else}https://www.heureka.cz{/if}" target="_blank" rel="noreferrer noopener">Heureka</a>
  </div>
  
  <div id="negative">
    <h3>{if isset($hodnoceni_negative_title)}{$hodnoceni_negative_title}{else}{l s='Mrzí nás, že nejste spokojeni' d='Modules.Mezistranka_hodnoceni.Rating'}{/if}</h3>
    <p>{if isset($hodnoceni_negative_text)}{$hodnoceni_negative_text}{else}{l s='Vaše zpětná vazba je pro nás velmi důležitá. Rádi bychom věděli, jak můžeme naše služby zlepšit. Prosím, kontaktujte nás přímo a pomůžeme najít řešení.' d='Modules.Mezistranka_hodnoceni.Rating'}{/if}</p>
    <a href="tel:{if isset($hodnoceni_phone)}{$hodnoceni_phone|replace:' ':''}{else}+420123456789{/if}">{l s='Telefon:' d='Modules.Mezistranka_hodnoceni.Rating'} {if isset($hodnoceni_phone)}{$hodnoceni_phone}{else}+420 123 456 789{/if}</a>
    <a href="mailto:{if isset($hodnoceni_email)}{$hodnoceni_email}{else}info@example.com{/if}">{l s='E-mail:' d='Modules.Mezistranka_hodnoceni.Rating'} {if isset($hodnoceni_email)}{$hodnoceni_email}{else}info@example.com{/if}</a>
  </div>
</div>