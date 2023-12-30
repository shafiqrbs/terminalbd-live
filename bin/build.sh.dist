#!/bin/sh

mkdir -p app/cache
mkdir -p app/logs
mkdir -p web/cache
mkdir -p web/images
mkdir -p web/uploads
mkdir -p web/uploads/files
mkdir -p web/uploads/user

echo "Removing old cache if any"
rm -Rf app/cache/*
rm -Rf app/logs/*


echo "(Re)Creating assets symlink"
rm web/assets
ln -s ../app/Resources/assets/ web/assets

echo "Database generated"
php app/console doctrine:schema:update --force

echo "Dumping assets"
app/console  assets:install --symlink --relative
app/console  assetic:dump --env=prod --no-debug

echo "Make directory writtable"
chmod -R 777 app/cache app/logs web/cache web/uploads web/images