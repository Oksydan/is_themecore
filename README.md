# Theme core module
Prestashop module created for [starter theme](https://github.com/Oksydan/modern-prestashop-starter-theme)

#### How to use assets.yml file

`assets.yml` file have to be placed inside `themes/THEME_NAME/config/` to work.
Example of `assets.yml` file:

```yml
css:
  product:
    fileName: product.css
    priority: 200
    include:
      - product
  checkout:
    fileName: checkout.css
    priority: 200
    include:
      - cart
      - order
      - orderconfirmation
  blog:
    fileName: blog.css
    priority: 200
    include:
      - module-blog-*
  example_remote_bootstrap:
    fileName: //cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css
    server: remote # required to set server: remote for remote file
    priority: 200

js:
  product:
    fileName: product.js
    priority: 200
    include:
      - product
  checkout:
    fileName: checkout.js
    priority: 200
    include:
      - cart
      - order
      - orderconfirmation
  blog:
    fileName: blog.js
    priority: 200
    include:
      - module-blog-*
  example_remote_bootstrap:
    fileName: //cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js
    server: remote # required to set server: remote for remote file
    priority: 200
```

You are able to use windcard `*` with include page name.


#### Structured data modification

You are able to modify structured data with hooks. List of hooks:
 - `actionStructuredDataBreadcrumb`
 - `actionStructuredDataProduct`
 - `actionStructuredDataShop`
 - `actionStructuredDataWebsite`

Every hook $param is an array with two keys:
 - `$data` - reference of structured data array
 - `$rawData` - raw structured data array (provided by data provider)

#### Partytown 

You are able to use [partytown](https://partytown.builder.io/) with this module. You have to enable it first in module configuration.
Example of usage for GTAG:

```html
    <script>
        window.partytown.forward.push('datalayer.push');
        window.partytown.forward.push('gtag');
    </script>
    <script type="text/partytown" src="https://www.googletagmanager.com/gtag/js?id=YOUR_GTAG_CODE"></script>
    <script type="text/partytown">
        dataLayer = window.dataLayer || [];
        window.gtag = function () {
            dataLayer.push(arguments);
        };

        window.gtag('js', new Date());

        window.gtag('config', 'YOUR_GTAG_CODE');
    </script>
```

##### Beware that partytown is still in beta, and it may not work as expected. Make sure to test it before using in production.

