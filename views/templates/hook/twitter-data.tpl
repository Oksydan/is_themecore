<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="{$page.meta.title}"/>
<meta property="twitter:description" content="{$page.meta.description}"/>
<meta property="twitter:site" content="{$shop.name}"/>
<meta property="twitter:creator" content="{$shop.name}"/>
<meta property="twitter:domain" content="{$urls.current_url}"/>

{if isset($product) && $page.page_name == 'product' && $product.default_image}
  <meta property="twitter:image" content="{$product.default_image.large.url}"/>
  <meta property="twitter:image:alt" content="{$page.meta.description}"/>
{elseif $page.page_name === 'category' && isset($category) && $category.image.large.url}
  <meta property="twitter:image" content="{$category.image.large.url}"/>
  <meta property="twitter:image:alt" content="{$page.meta.description}"/>
{else}
  <meta property="twitter:image" content="{$urls.shop_domain_url}{$shop.logo}"/>
  <meta property="twitter:image:alt" content="{$page.meta.description}"/>
{/if}
