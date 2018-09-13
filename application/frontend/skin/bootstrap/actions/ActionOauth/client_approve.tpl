{**
 * Страница входа
 *}

{extends 'layouts/layout.base.tpl'}

{block 'layout_page_title'}
    {$aLang.oauth.approve.title}
{/block}

{block 'layout_content'}
    
    <form class="navbar-form pull-left">
        {component 'app' header="Название" content="Описание" image={$image|default:$sAppDefImage}}
        <br>
        {foreach $aScopes as $oScope}
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                <label class="form-check-label" for="defaultCheck1">
                   {$oScope->getDescription()}
                </label>
            </div>
        {/foreach}
        <br>
        <button type="submit" class="btn btn-primary">{lang 'oauth.approve.submit.text'}</button>
    </form>
{/block}