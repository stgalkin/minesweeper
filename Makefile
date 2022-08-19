
# Code style checking
review:
	./vendor/bin/phpcs --standard=phpcs.xml src/

review-tests:
	./vendor/bin/phpcs --standard=phpcs.xml tests/

# Code fix
fix-code:
	./vendor/bin/phpcbf --standard=phpcs.xml src/

fix-tests:
	./vendor/bin/phpcbf --standard=phpcs.xml tests/

# Code analysis
analyse:
	vendor/bin/phpstan analyse -l 4 --memory-limit 1G .
	vendor/bin/psalm
