{**
 * Страница входа
 *}

{extends 'layouts/layout.base.tpl'}

{block 'layout_page_title'}
    {$aLang.oauth.approve.title}
{/block}

{block 'layout_content'}
    
    <form class="navbar-form pull-left" method="post" >
        {component 'app' header="{$oClient->getName()}" content="{$oClient->getDescription()}" image={$image|default:$sAppDefImage}}
        <br>
        {foreach $aScopes as $oScope}
            <div class="form-check">
                {component 'field.checkbox' label="{$oScope->getDescription()}" name="scopes[{$oScope->getId()}]" checked=1}
            </div>
        {/foreach}
        <br>
        {component 'field.hidden' name="approve" value=1}
        {component 'button' text={lang 'oauth.approve.submit.text'} mods="primary"}
        {component 'button' text={lang 'common.cancel'} url={$oClient->getRedirectUri()} }
    </form>
{/block}