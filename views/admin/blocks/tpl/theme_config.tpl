[{$smarty.block.parent}]
[{if $var_group == "display"}]
    [{assign var="oConfig" value=$oView->getConfig()}]
    [{assign var="sVariables" value=$oConfig->getShopConfVar('sVariables')}]
    [{assign var="aVariables" value=","|explode:$sVariables}]
    [{foreach from=$aVariables item=theme_var}]
    <dl>
        <dt>
            <input type=text class="txt" style="width: 250px;" name=confstrs[[{$theme_var}]] value="[{$confstrs.$theme_var}]" [{ $readonly }]>
            [{oxinputhelp ident="HELP_SHOP_THEME_`$theme_var`"}]
        </dt>
        <dd>
            [{oxmultilang ident="SHOP_THEME_`$theme_var`"}]
        </dd>
        <div class="spacer"></div>
    </dl>
    [{/foreach}]
[{/if}]