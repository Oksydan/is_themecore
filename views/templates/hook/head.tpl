{include file="module:is_themecore/views/template/hook/og-data.tpl"}
{include file="module:is_themecore/views/template/hook/twitter-data.tpl"}

{foreach from=$jsonData item=jsonElem}
    <script type="application/ld+json">
      {$jsonElem nofilter}
    </script>
{/foreach}
