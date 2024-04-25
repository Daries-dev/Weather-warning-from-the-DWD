<h2 class="boxTitle">{lang}wcf.weatherWarning.dwd.region{/lang}</h2>

<div class="pointer" data-weather-info-full-view="{lang}wcf.weatherWarning.dwd.region{/lang}"
    title="{lang}wcf.weatherWarning.viewFullSize{/lang}">
    <div class="weatherWarningContent">
        {if !$region|empty}
            {foreach from=$warnings item=warning}
                <div class="weatherWarningRegion">
                    <div class="headline">
                        <img src="{@$__wcf->getPath()}images/weather/{$warning[event]}.png" alt="">
                        <span>{$warning[headline]}</span>
                    </div>

                    <div class="warnLevel">
                        <div class="warnColor"></div>
                        <div class="levelRules level{@$warning[level]}"></div>
                    </div>

                    <dl class="plain dataList containerContent">
                        <dt><label>{lang}wcf.weatherWarning.start{/lang}</label></dt>
                        <dd>{@$warning[start]|plainTime}</dd>

                        <dt><label>{lang}wcf.weatherWarning.end{/lang}</label></dt>
                        <dd>{@$warning[end]|plainTime}</dd>
                    </dl>

                    <div class="description small">
                        {$warning[description]}
                    </div>

                    {if !$warning[instruction]|empty}
                        <div class="instruction small">
                            {$warning[instruction]}
                        </div>
                    {/if}
                </div>
            {foreachelse}
                <p>{lang}wcf.weatherWarning.empty{/lang}</p>
            {/foreach}
        {else}
            <p>{lang}wcf.weatherWarning.empty.region{/lang}</p>
        {/if}
    </div>
</div>