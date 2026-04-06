mkdocs-serve:
	@if command -v mkdocs >/dev/null ; then \
		mkdocs serve; \
	else \
		echo "mkdocs is not installed, see https://docs.openmage.org/developers/mkdocs/" && exit 2; \
	fi; \

phpunit-serve:
	@if test -f build/phpunit/coverage/index.html ; then \
		xdg-open build/phpunit/coverage/index.html; \
	else \
		echo "phpunit coverage not found, run \"composer run phpunit:coverage-local\"" && exit 2; \
	fi; \

phpmd-serve:
	@if test -f build/phpmd/report.html ; then \
		xdg-open build/phpmd/report.html; \
	else \
		echo "phpmd report not found, run \"composer run phpmd\"" && exit 2; \
	fi; \
