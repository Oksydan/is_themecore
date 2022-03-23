{include file="module:is_themecore/views/templates/hook/og-data.tpl"}
{include file="module:is_themecore/views/templates/hook/twitter-data.tpl"}


{foreach $jsonData as $jsonElem}
  {if $jsonElem|trim}
    <script type="application/ld+json">
      {$jsonElem nofilter}
    </script>
  {/if}
{/foreach}
