<meta property="og:title" content="{$page.meta.title}"/>
<meta property="og:description" content="{$page.meta.description}"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="{$urls.current_url}"/>
<meta property="og:site_name" content="{$shop.name}"/>

{if isset($product) && $page.page_name == 'product'}
  <meta property="og:type" content="product"/>
  {if $product.images|count > 0}
    {foreach from=$product.images item=p_img name="p_img_list"}
      <meta property="og:image" content="{$p_img.large.url}"/>
    {/foreach}
    <meta property="og:image:height" content="{$p_img.large.height}"/>
    <meta property="og:image:width" content="{$p_img.large.width}"/>

  {/if}
  {if $product.show_price}
    <meta property="product:price:amount" content="{$product.price_amount}" />
    <meta property="product:price:currency" content="{$currency.iso_code}" />
    {if $product.has_discount}
      <meta property="product:price:standard_amount" content="{$product.regular_price_amount}" />
    {/if}
  {/if}
  {if $product_manufacturer->name}
    <meta property="product:brand" content="{$product_manufacturer->name|escape:'html':'UTF-8'}" />
  {/if}
  <meta property="og:availability" content="{if $product.quantity_all_versions > 0 || $product.allow_oosp > 0}instock{else}out of stock{/if}" />
{elseif $page.page_name === 'category' && isset($category) && $category.image.large.url}
  <meta property="og:image" content="{$category.image.large.url}"/>
{else}
  <meta property="og:image" content="{$urls.shop_domain_url}{$shop.logo}"/>
{/if}
