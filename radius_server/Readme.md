
### Instalando os serviços radius + mysql
```
sudo apt -y  install git freeradius freeradius-mysql freeradius-utils mariadb-server 
sudo systemctl enable --now freeradius
```


### habilitando o firewall
```
sudo ufw enable
sudo ufw allow to any port 1812 proto udp
sudo ufw allow to any port 1813 proto udp
sudo ufw allow http
sudo ufw allow ssh
```


### configurar os clientes radius
```
cd /etc/freeradius/3.0/
sudo vim clients.conf
```


### criar o usuário radius para acessar o mysql
```
sudo mysql -u root -e "CREATE DATABASE radius;"
sudo mysql -u root -e "CREATE USER 'radius'@'localhost' IDENTIFIED BY 'radius';"
sudo mysql -u root -e "GRANT ALL PRIVILEGES ON radius.* TO 'radius'@'localhost';"
sudo mysql -u root -e "FLUSH PRIVILEGES;"
```


### importar o schema do banco radius
```
sudo mysql -u root -p radius < /etc/freeradius/3.0/mods-config/sql/main/mysql/schema.sql
sudo mysql -u root -p -e "use radius; show tables"
```



### habilitar o módulo sql
```
sudo ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/
vi /etc/freeradius/3.0/mods-enabled/sql
sudo chgrp -h freerad /etc/freeradius/3.0/mods-available/sql
sudo chown -R freerad:freerad /etc/freeradius/3.0/mods-enabled/sql
sudo systemctl restart freeradius
```


### instalar o nginx e php
```
sudo apt install -y nginx php8.1-fpm php-mysql
```


### Configura o nginx para buscar o php
```
# ditar o arquivo /etc/nginx/sites-enabled/default, deixá-lo com o seguinte conteúdo:
server {
        listen 80 default_server;
        listen [::]:80 default_server;

        root /var/www/html;
        index index.php;
        server_name _;
        location / {
                try_files $uri $uri/ =404;
        }
        location ~ \.php$ {
                include fastcgi.conf;
                fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        }
}

```


### clonar os arquivos web
```
git clone https://github.com/CitraIT/opnsense-hotspot
sudo mv opnsense-hotspot/radius_server/var/www/html/* /var/www/html/
sudo chown -R www-data:www-data /var/www/html/
```


### configurar o timezone do php
```
sudo vim /etc/php/8.1/fpm/php.ini    -> date.timezone = America/Sao_Paulo
```

### ajustar os dados do banco de dados
```
sudo vim /var/www/html/db.php
```

### reiniciar os serviços
```
systemctl restart nginx php8.1-fpm
```


### OPCIONAL: instalar o certificado lets encrypt
```
sudo apt install -y certbot python3-certbot-nginx
sudo certbot -d auth.citrait.com.br
```

