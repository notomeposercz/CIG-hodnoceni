{extends file='page.tpl'}

{block name='page_title'}
  {l s='Ohodnoťte nás' d='Modules.Mezistranka_hodnoceni.Rating'}
{/block}

{block name='page_content'}
  {include file='module:mezistranka_hodnoceni/views/templates/front/rating_content.tpl'}
{/block}