{include file="module:is_themecore/views/templates/hook/og-data.tpl"}
{include file="module:is_themecore/views/templates/hook/twitter-data.tpl"}


{foreach $jsonData as $jsonElem}
  {if $jsonElem|trim}
    <script type="application/ld+json">
      {$jsonElem nofilter}
    </script>
  {/if}
{/foreach}

{if $loadPartytown && $partytownScript && $partytownScriptUri}
  <script>
    if (typeof window.partytown === 'undefined') {
      window.partytown = {};
    }

    window.partytown.forward = [];
    window.partytown.lib = {$partytownScriptUri};

    {if isset($debugPartytown) && $debugPartytown}
        window.partytown.debug = true;
        window.partytown.logScriptExecution = true;
        window.partytown.logCalls = true;
        window.partytown.logGetters = true;
        window.partytown.logSetters = true;
        window.partytown.logImageRequests = true;
        window.partytown.logScriptExecution = true;
        window.partytown.logScriptExecution = true;
        window.partytown.logSendBeaconRequests = true;
    {/if}
  </script>

  <script>
    {$partytownScript nofilter}
  </script>
{/if}
