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
