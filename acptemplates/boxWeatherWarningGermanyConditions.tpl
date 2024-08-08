<section class="section">
    <h2 class="sectionTitle">{lang}wcf.acp.box.settings{/lang}</h2>

    <dl>
        <dt><label for="map">{lang}wcf.weatherWarning.box.settings{/lang}</label></dt>
        <dd>
            <select name="map">
                {foreach from=$mapOptions key=optionKey item=optionValue}
                    <option value="{$optionKey}" {if $map === $optionKey} selected{/if}>{$optionValue}</option>
                {/foreach}
            </select>
        </dd>
    </dl>

    {event name='fields'}
</section>