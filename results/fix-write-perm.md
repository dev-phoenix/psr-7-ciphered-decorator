# fix wraite premission for apache user

```sh

setfacl -m u:www-data:rwx .
getfacl .

```