<h2 class="boxTitle">{lang}wcf.weatherWarning.dwd.region{/lang}</h2>

<div class="pointer" data-weather-info-full-view="{lang}wcf.weatherWarning.dwd.region{/lang}"
    title="{lang}wcf.weatherWarning.viewFullSize{/lang}">
    <div class="weatherWarningContent">
        {if !$region|empty}
            {foreach from=$warnings item=warning}
                <div class="weatherWarningRegion warningBox">
                    <div class="headline">
                        {@$warning->getIcon()}
                        <span>{$warning->getHeadline()}</span>
                    </div>

                    <div class="warnLevel">
                        <div class="warnColor"></div>
                        <div class="levelRules level{$warning->getLevel()}"></div>
                    </div>

                    <dl class="plain dataList containerContent">
                        <dt><label>{lang}wcf.weatherWarning.start{/lang}</label></dt>
                        <dd>{@$warning->getStart()|plainTime}</dd>

                        <dt><label>{lang}wcf.weatherWarning.end{/lang}</label></dt>
                        <dd>{@$warning->getEnd()|plainTime}</dd>
                    </dl>

                    <div class="description small">
                        {$warning->getDescription()}
                    </div>

                    {if !$warning->getInstruction()|empty}
                        <div class="instruction small">
                            {$warning->getInstruction()}
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

<div class="weatherWarningFooter">
    <div class="allWarnings">{lang}wcf.weatherWarning.allWarnings{/lang}</div>
</div>