{extends file='page.tpl'}

{block name='page_title'}
  {l s='Ohodnoťte nás' d='Modules.Mezistranka_hodnoceni.Rating'}
{/block}

{block name='page_content'}
  <div class="rating-container">
    <h2>{l s='Jak jste spokojeni s našimi službami?' d='Modules.Mezistranka_hodnoceni.Rating'}</h2>
    <div id="starContainer" class="stars">
      <span data-star="1">★</span>
      <span data-star="2">★</span>
      <span data-star="3">★</span>
      <span data-star="4">★</span>
      <span data-star="5">★</span>
    </div>
    
    <div id="positive">
      <h3>{l s='Děkujeme za pozitivní hodnocení!' d='Modules.Mezistranka_hodnoceni.Rating'}</h3>
      <p>{l s='Pomohli byste nám sdílet vaši spokojenost i jinde?' d='Modules.Mezistranka_hodnoceni.Rating'}</p>
      <a href="https://g.page/r/vase-google-recenze" target="_blank">Google</a>
      <a href="https://www.firmy.cz/vase-firma" target="_blank">Seznam</a>
      <a href="https://obchody.heureka.cz/vas-eshop/recenze" target="_blank">Heureka</a>
    </div>
    
    <div id="negative">
      <h3>{l s='Mrzí nás, že nejste spokojeni' d='Modules.Mezistranka_hodnoceni.Rating'}</h3>
      <p>{l s='Pomozte nám to napravit, prosím kontaktujte nás přímo.' d='Modules.Mezistranka_hodnoceni.Rating'}</p>
      <a href="{$urls.pages.contact}">{l s='Kontaktujte nás' d='Modules.Mezistranka_hodnoceni.Rating'}</a>
    </div>
  </div>
{/block}