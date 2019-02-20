# Semlohe

Semacam SSO service based on oauth2

### Requirement
- php7.2
- mysql based database
- composer
- npm - node

### Installation
- ``composer install``
- ketik ``./migrate_dev.sh``
- Tambahkan file ``.env`` dengan isi sebagai berikut
```
ENV="<dev | prod>"
```

- Atur file konfigurasi pada ``config/env/`` sesuai dengan environment
- Siyap development

### Javascript
- Semua source javascript ada di direktori ``source/admin``
- Requirement : nodejs, yarn / npm, gulp 3.x.x
- Setup : ``npm install`` untuk npm atau ``yarn install`` untuk yarn
- Live watch : ``npm run admin`` untuk npm atau ``yarn run admin`` untuk yarn 