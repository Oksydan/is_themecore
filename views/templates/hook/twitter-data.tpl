<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="{$page.meta.title}"/>
{if !empty($page.meta.description)}
  <meta property="twitter:description" content="{$page.meta.description}"/>
{/if}
<meta property="twitter:site" content="{$shop.name}"/>
<meta property="twitter:creator" content="{$shop.name}"/>
<meta property="twitter:domain" content="{$urls.current_url}"/>

{if isset($product) && $page.page_name == 'product' && !empty($product.default_image)}
  <meta property="twitter:image" content="{$product.default_image.large.url}"/>
  {if !empty($page.meta.description)}
    <meta property="twitter:image:alt" content="{$page.meta.description}"/>
  {/if}
{elseif $page.page_name === 'category' && isset($category) && !empty($category.image.large.url)}
  <meta property="twitter:image" content="{$category.image.large.url}"/>
  {if !empty($page.meta.description)}
    <meta property="twitter:image:alt" content="{$page.meta.description}"/>
  {/if}
{else}
  <meta property="twitter:image" content="{$shop.logo}"/>
  {if !empty($page.meta.description)}
    <meta property="twitter:image:alt" content="{$page.meta.description}"/>
  {/if}
{/if}
