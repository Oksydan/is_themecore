{foreach from=$jsonData item=jsonElem}
  <script type="application/ld+json">
    {$jsonElem nofilter}
  </script>
{/foreach}
