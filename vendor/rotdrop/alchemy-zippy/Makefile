adapters:="ZipAdapter" "ZipExtensionAdapter" "GNUTar\\TarGNUTarAdapter" "GNUTar\\TarGzGNUTarAdapter" "GNUTar\\TarBz2GNUTarAdapter" "BSDTar\\TarBSDTarAdapter" "BSDTar\\TarGzBSDTarAdapter" "BSDTar\\TarBz2BSDTarAdapter"

.PHONY: test clean

test: node_modules
	-./tests/bootstrap.sh stop
	./tests/bootstrap.sh start
	sleep 1
	./vendor/bin/phpunit --display-all-issues
	FAILURES="";$(foreach adapter,$(adapters),ZIPPY_ADAPTER=$(adapter) ./vendor/bin/phpunit -c phpunit-functional.xml --display-all-issues || FAILURES=1;)test -z "$$FAILURES"
	-./tests/bootstrap.sh stop

node_modules:
	npm install connect serve-static

clean:
	rm -rf node_modules
