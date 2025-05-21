{*
* Souhrnné statistiky pro zobrazení v administraci
*}

<div class="dashboard-stats">
    <div class="statistics-summary">
        <div class="stat-box total-box">
            <h5>{l s='Celkem hodnocení' mod='mezistranka_hodnoceni'}</h5>
            <div class="stat-value">{$total_ratings}</div>
        </div>
        <div class="stat-box average-box">
            <h5>{l s='Průměrné hodnocení' mod='mezistranka_hodnoceni'}</h5>
            <div class="stat-value">{$average_rating|string_format:"%.1f"}</div>
            <div class="rating-stars">
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
        <div class="stat-box positive-box">
            <h5>{l s='Pozitivní hodnocení (4-5)' mod='mezistranka_hodnoceni'}</h5>
            <div class="stat-value">{$positive_count}</div>
            <div class="stat-percent">{$positive_percent|string_format:"%.1f"}%</div>
        </div>
        <div class="stat-box negative-box">
            <h5>{l s='Negativní hodnocení (1-3)' mod='mezistranka_hodnoceni'}</h5>
            <div class="stat-value">{$negative_count}</div>
            <div class="stat-percent">{$negative_percent|string_format:"%.1f"}%</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon icon-bar-chart"></i> {l s='Rozložení hodnocení' mod='mezistranka_hodnoceni'}
            </div>
            <div class="panel-body">
                <div class="rating-distribution">
                    {foreach from=$rating_distribution key=rating item=data}
                        <div class="rating-row">
                            <div class="rating-stars-small">
                                {for $i=1 to 5}
                                    {if $i <= $rating}<i class="icon icon-star"></i>{else}<i class="icon icon-star-o"></i>{/if}
                                {/for}
                            </div>
                            <div class="rating-bar-container">
                                <div class="rating-bar" style="width: {$data.percent}%"></div>
                            </div>
                            <div class="rating-percent">{$data.percent|string_format:"%.1f"}%</div>
                            <div class="rating-count">({$data.count})</div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
</div>

{if !empty($link_clicks)}
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon icon-link"></i> {l s='Konverze na odkazy' mod='mezistranka_hodnoceni'}
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-statistics">
                    <thead>
                        <tr>
                            <th>{l s='Odkaz' mod='mezistranka_hodnoceni'}</th>
                            <th class="text-center" style="width: 120px">{l s='Počet kliknutí' mod='mezistranka_hodnoceni'}</th>
                            <th class="text-center" style="width: 120px">{l s='Konverzní poměr' mod='mezistranka_hodnoceni'}</th>
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
</div>
{/if}