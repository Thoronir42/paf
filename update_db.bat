RMDIR /s /q .\temp\cache

::php ./www/index.php migrations:diff

php ./www/index.php orm:schema-tool:update --force