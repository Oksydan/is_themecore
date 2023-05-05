build-module-zip: build-composer build-assets build-zip

build-zip:
	rm -rf is_themecore.zip
	cp -Ra $(PWD) /tmp/is_themecore
	rm -rf /tmp/is_themecore/config_*.xml
	rm -rf /tmp/is_themecore/_theme_dev/node_modules
	rm -rf /tmp/is_themecore/_module_dev/node_modules
	rm -rf /tmp/is_themecore/.github
	rm -rf /tmp/is_themecore/.gitignore
	rm -rf /tmp/is_themecore/.php-cs-fixer.cache
	rm -rf /tmp/is_themecore/.git
	mv -v /tmp/is_themecore $(PWD)/is_themecore
	zip -r is_themecore.zip is_themecore
	rm -rf $(PWD)/is_themecore

build-composer:
	composer install --no-dev -o

build-assets:
	cd _module_dev && . ${HOME}/.nvm/nvm.sh && nvm install && npm install && npm run build
