<div class="pointer" data-weather-info-full-view="{lang}wcf.weatherWarning.dwd{/lang}"
    title="{lang}wcf.weatherWarning.viewFullSize{/lang}">
    <div class="weatherWarningMap">
        <img src="{@$germanyMap}" class="germanyMap" alt="">

        <div class="germanyMapInfo">
            <div class="legendBoxContainer">
                <div>
                    <div class="legendBox" style="--weatherWarningLegendColor: rgb(118, 30, 77)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend1{/lang}</div>
                </div>
                <div>
                    <div class="legendBox" style="--weatherWarningLegendColor: rgb(209, 74, 71)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend2{/lang}</div>
                </div>
                <div>
                    <div class="legendBox" style="--weatherWarningLegendColor: rgb(200, 132, 50)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend3{/lang}</div>
                </div>
                <div>
                    <div class="legendBox" style="--weatherWarningLegendColor: rgb(102, 75, 0)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend4{/lang}</div>
                </div>
                <div>
                    <div class="legendBox preliminary" style="--weatherWarningLegendColor: rgb(255, 255, 255)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend5{/lang}</div>
                </div>
                {if $__wcf->getLanguage()->getFixedLanguageCode() === "de"}
                    <div>
                        <div class="legendBox" style="--weatherWarningLegendColor: rgb(122, 49, 196)"></div>
                        <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend6{/lang}</div>
                    </div>
                {/if}
                <div>
                    <div class="legendBox" style="--weatherWarningLegendColor: rgb(77, 31, 122)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend7{/lang}</div>
                </div>
                <div>
                    <div class="legendBox" style="--weatherWarningLegendColor: rgb(163, 41, 163)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend8{/lang}</div>
                </div>
                <div>
                    <div class="legendBox" style="--weatherWarningLegendColor: rgb(149, 184, 46)"></div>
                    <div class="legendBoxText">{lang}wcf.weatherWarning.dwd.legend9{/lang}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="weatherWarningMapButton">
    <a href="https://www.dwd.de/DE/wetter/warnungen_gemeinden/warnWetter_node.htm" class="button"
        {if EXTERNAL_LINK_TARGET_BLANK} target="_blank" {/if}>
        {icon name='info' size=24}
        <span>{lang}wcf.weatherWarning.more.information{/lang}</span>
    </a>
</div>