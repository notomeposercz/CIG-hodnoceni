{if $admin_css}
<style>
{$admin_css nofilter}
</style>
{/if}

{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="bootstrap panel">
    <div class="panel-heading">
        <i class="icon icon-star"></i> {l s='Mezistranka hodnoceni' mod='mezistranka_hodnoceni'}
    </div>
    
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item {if $current_tab == 'configuration'}active{/if}">
            <a class="nav-link" href="{$config_url|escape:'html':'UTF-8'}" role="tab">
                <i class="icon icon-cog"></i> {l s='Konfigurace' mod='mezistranka_hodnoceni'}
            </a>
        </li>
        <li class="nav-item {if $current_tab == 'statistics'}active{/if}">
            <a class="nav-link" href="{$statistics_url|escape:'html':'UTF-8'}" role="tab">
                <i class="icon icon-bar-chart"></i> {l s='Statistiky hodnocení' mod='mezistranka_hodnoceni'}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {* Záložka konfigurace *}
        <div class="tab-pane {if $current_tab == 'configuration'}active{/if}" id="configuration">
            <div class="panel-body">
                <p>
                    <strong>{l s='Nastavení modulu mezistránky pro hodnocení zákazníků' mod='mezistranka_hodnoceni'}</strong><br />
                    {l s='Tento modul zobrazuje mezistránku s hvězdičkovým hodnocením.' mod='mezistranka_hodnoceni'}<br />
                    {l s='Pokud zákazník udělí 4-5 hvězdiček, nabídne externí recenzní služby.' mod='mezistranka_hodnoceni'}<br />
                    {l s='Pokud zákazník udělí 1-3 hvězdičky, nabídne pouze kontaktní formulář.' mod='mezistranka_hodnoceni'}
                </p>
                <p>
                    {l s='Pro funkční modul je potřeba vytvořit stránku s následujícím obsahem:' mod='mezistranka_hodnoceni'}
                </p>
                <pre>
&lt;div class="rating-container"&gt;
  &lt;h2&gt;Jak jste spokojeni s našimi službami?&lt;/h2&gt;
  &lt;div id="starContainer" class="stars"&gt;
    &lt;span&gt;★&lt;/span&gt;
    &lt;span&gt;★&lt;/span&gt;
    &lt;span&gt;★&lt;/span&gt;
    &lt;span&gt;★&lt;/span&gt;
    &lt;span&gt;★&lt;/span&gt;
  &lt;/div&gt;
  
  &lt;div id="positive"&gt;
    &lt;h3&gt;Děkujeme za pozitivní hodnocení!&lt;/h3&gt;
    &lt;p&gt;Pomohli byste nám sdílet vaši spokojenost i jinde?&lt;/p&gt;
    &lt;a href="https://www.google.com/search?..." target="_blank"&gt;Google&lt;/a&gt;
    &lt;a href="https://www.firmy.cz/..." target="_blank"&gt;Seznam&lt;/a&gt;
    &lt;a href="https://obchody.heureka.cz/..." target="_blank"&gt;Heureka&lt;/a&gt;
  &lt;/div&gt;
  
  &lt;div id="negative"&gt;
    &lt;h3&gt;Mrzí nás, že nejste spokojeni&lt;/h3&gt;
    &lt;p&gt;Pomozte nám to napravit, prosím kontaktujte nás přímo.&lt;/p&gt;
    &lt;a href="tel:+420..."&gt;Telefon: +420...&lt;/a&gt;
    &lt;a href="mailto:..."&gt;E-mail: ...&lt;/a&gt;
  &lt;/div&gt;
&lt;/div&gt;
                </pre>
            </div>
        </div>
        
        {* Záložka statistiky *}
<div class="tab-pane {if $current_tab == 'statistics'}active{/if}" id="statistics">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h3>{l s='Statistiky hodnocení zákazníků' mod='mezistranka_hodnoceni'}</h3>
                <hr>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-md-12 text-right">
                <a href="{$export_url|escape:'html':'UTF-8'}" class="btn btn-primary" id="export-stats-btn">
                    <i class="icon icon-download"></i> {l s='Exportovat statistiky (CSV)' mod='mezistranka_hodnoceni'}
                </a>
            </div>
        </div>
        
        {if empty($statistics)}
            <div class="alert alert-info">
                {l s='Zatím nejsou k dispozici žádné statistiky hodnocení.' mod='mezistranka_hodnoceni'}
            </div>
        {else}
            {* 1. část - Hlavní souhrn statistik *}
            <div class="row stats-summary-container stats-summary-main">
                <div class="col-md-12">
                    <div class="stats-summary stats-summary-row">
                        <div class="stats-box stats-box-custom stats-box-purple stats-total-ratings">
                            <div class="stats-box-title">{l s='Celkem hodnocení' mod='mezistranka_hodnoceni'}</div>
                            <div class="stats-box-value">{$total_ratings}</div>
                        </div>
                        
                        <div class="stats-box stats-box-custom stats-box-blue stats-average-rating">
                            <div class="stats-box-title">{l s='Průměrné hodnocení' mod='mezistranka_hodnoceni'}</div>
                            <div class="stats-box-value">{$average_rating|string_format:"%.1f"}</div>
                            <div class="stats-rating-stars stats-average-stars">
                                {for $i=1 to 5}
                                    {if $i <= $average_rating}
                                        <i class="icon icon-star"></i>
                                    {elseif $i <= $average_rating+0.5}
                                        <i class="icon icon-star-half-o"></i>
                                    {else}
                                        <i class="icon icon-star-o"></i>
                                    {/if}
                                {/for}
                            </div>
                        </div>
                        
                        <div class="stats-box stats-box-custom stats-box-green stats-positive-ratings">
                            <div class="stats-box-title">{l s='Pozitivní hodnocení (4-5)' mod='mezistranka_hodnoceni'}</div>
                            <div class="stats-box-value">{$positive_count}</div>
                            <div class="stats-box-percent stats-positive-percent">{$positive_percent|string_format:"%.1f"}%</div>
                        </div>
                        
                        <div class="stats-box stats-box-custom stats-box-orange stats-negative-ratings">
                            <div class="stats-box-title">{l s='Negativní hodnocení (1-3)' mod='mezistranka_hodnoceni'}</div>
                            <div class="stats-box-value">{$negative_count}</div>
                            <div class="stats-box-percent stats-negative-percent">{$negative_percent|string_format:"%.1f"}%</div>
                        </div>
                    </div>
                </div>
            </div>
            
            {* 2. a 3. část - Rozložení hodnocení a Konverze na odkazy *}
            <div class="row stats-details-container">
                {* 2. část - Rozložení hodnocení *}
                <div class="col-md-6 stats-distribution-column">
                    <div class="stats-panel stats-rating-distribution-panel">
                        <div class="stats-panel-heading">
                            <i class="icon icon-bar-chart"></i> {l s='Rozložení hodnocení' mod='mezistranka_hodnoceni'}
                        </div>
                        <div class="stats-panel-body stats-distribution-body">
                            <div class="rating-distribution stats-rating-distribution">
                                {foreach from=$rating_distribution key=rating item=data}
                                    <div class="rating-row stats-rating-row">
                                        <div class="rating-stars-block stats-rating-stars-block">
                                            {for $i=1 to 5}
                                                {if $i <= $rating}<i class="icon icon-star"></i>{else}<i class="icon icon-star-o"></i>{/if}
                                            {/for}
                                        </div>
                                        <div class="rating-progress-container stats-rating-progress-container">
                                            <div class="rating-progress-bar stats-rating-progress-bar" style="width: {$data.percent}%"></div>
                                        </div>
                                        <div class="rating-percentage stats-rating-percentage">{$data.percent|string_format:"%.1f"}%</div>
                                        <div class="rating-count stats-rating-count">({$data.count})</div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
                
                {* 3. část - Konverze na odkazy *}
                {if !empty($link_clicks)}
                <div class="col-md-6 stats-links-column">
                    <div class="stats-panel stats-links-conversion-panel">
                        <div class="stats-panel-heading">
                            <i class="icon icon-link"></i> {l s='Konverze na odkazy' mod='mezistranka_hodnoceni'}
                        </div>
                        <div class="stats-panel-body stats-links-body">
                            <table class="stats-table stats-links-table">
                                <thead>
                                    <tr>
                                        <th>{l s='Odkaz' mod='mezistranka_hodnoceni'}</th>
                                        <th class="text-center">{l s='Počet kliknutí' mod='mezistranka_hodnoceni'}</th>
                                        <th class="text-center">{l s='Konverzní poměr' mod='mezistranka_hodnoceni'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$link_clicks key=link item=data}
                                        <tr>
                                            <td>{$link}</td>
                                            <td class="text-center">{$data.count}</td>
                                            <td class="text-center">{$data.percent|string_format:"%.1f"}%</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {/if}
            </div>
            
            {* 4. část - Detail hodnocení *}
            <div class="stats-detail-container stats-full-detail">
                <div class="stats-detail-title">
                    <i class="icon icon-list"></i> {l s='Detail hodnocení' mod='mezistranka_hodnoceni'}
                </div>
                
                <div class="filter-container stats-filter-container">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="filter-label">{l s='Filtrovat podle hodnocení:' mod='mezistranka_hodnoceni'}</label>
                            <select class="form-control rating-filter stats-rating-filter">
                                <option value="all">{l s='Všechna hodnocení' mod='mezistranka_hodnoceni'}</option>
                                <option value="5">★★★★★ (5/5)</option>
                                <option value="4">★★★★☆ (4/5)</option>
                                <option value="3">★★★☆☆ (3/5)</option>
                                <option value="2">★★☆☆☆ (2/5)</option>
                                <option value="1">★☆☆☆☆ (1/5)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive stats-table-responsive">
                    <table class="table table-striped stats-table stats-full-detail-table">
                        <thead>
                            <tr>
                                <th>{l s='ID' mod='mezistranka_hodnoceni'}</th>
                                <th>{l s='Hodnocení' mod='mezistranka_hodnoceni'}</th>
                                <th>{l s='Odkaz' mod='mezistranka_hodnoceni'}</th>
                                <th>{l s='Zákazník' mod='mezistranka_hodnoceni'}</th>
                                <th>{l s='IP adresa' mod='mezistranka_hodnoceni'}</th>
                                <th>{l s='Datum' mod='mezistranka_hodnoceni'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$statistics item=stat}
                                <tr class="stat-row" data-rating="{$stat.rating}">
                                    <td>{$stat.id_stat}</td>
                                    <td class="rating-stars-table stats-rating-stars-table">
                                        {for $i=1 to 5}
                                            {if $i <= $stat.rating}<i class="icon icon-star"></i>{else}<i class="icon icon-star-o"></i>{/if}
                                        {/for}
                                        <span class="ml-1">({$stat.rating}/5)</span>
                                    </td>
                                    <td>{if $stat.clicked_link}{$stat.clicked_link}{else}<em>{l s='Žádný' mod='mezistranka_hodnoceni'}</em>{/if}</td>
                                    <td>
                                        {if $stat.id_customer}
                                            <a href="{$link->getAdminLink('AdminCustomers')|escape:'html':'UTF-8'}&id_customer={$stat.id_customer}&viewcustomer" target="_blank">
                                                {$stat.firstname} {$stat.lastname} ({$stat.customer_email})
                                            </a>
                                        {else}
                                            <em>{l s='Nepřihlášený' mod='mezistranka_hodnoceni'}</em>
                                        {/if}
                                    </td>
                                    <td>{$stat.ip_address}</td>
                                    <td>{$stat.date_add}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
    </div>
</div>