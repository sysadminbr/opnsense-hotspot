### Pré-Requisitos  
➡️ S.O Ubuntu 22.04 LTS  
➡️ Acesso a Internet


### Instalando os serviços radius + mysql
```
# instala o radius e mysql-server
sudo apt -y  install git freeradius freeradius-mysql freeradius-utils mariadb-server 

# habilita e inicia os serviços
sudo systemctl enable --now freeradius
```


### Habilitando o firewall
```
# habilita o ufw e libera as portas de auth/account, além do http, https e ssh.
sudo ufw enable
sudo ufw allow to any port 1812 proto udp
sudo ufw allow to any port 1813 proto udp
sudo ufw allow http
sudo ufw allow https
sudo ufw allow ssh
```


### Configurar os clientes radius (NAS)
```
# acessa a pasta de configuração do radius
cd /etc/freeradius/3.0/

# edita a configuração de clientes/nas
sudo vim clients.conf

# Adicionar as linhas abaixo para permitir o acesso de qualquer NAS
cliente permite_tudo {
	ipaddr = *
	secret = password123
}
```


### Criar o login radius dentro do MySQL
```
sudo mysql -u root -e "CREATE DATABASE radius;"
sudo mysql -u root -e "CREATE USER 'radius'@'localhost' IDENTIFIED BY 'radius';"
sudo mysql -u root -e "GRANT ALL PRIVILEGES ON radius.* TO 'radius'@'localhost';"
sudo mysql -u root -e "FLUSH PRIVILEGES;"
```


### Importar o schema (esqueleto) do banco de dados
```
sudo mysql -u root -p radius < /etc/freeradius/3.0/mods-config/sql/main/mysql/schema.sql
sudo mysql -u root -p -e "use radius; show tables"
```



### Habilitar o módulo sql dentro do radius
```
# habilita o módulo que existe, mas atualmente desativado
sudo ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/

# editar a configuração do sql
vi /etc/freeradius/3.0/mods-enabled/sql

# ajustar as linhas conforme abaixo na parte do sql
sql {
	dialect = "mysql"
	driver = "rlm_sql_${dialect}"
	mysql {
		# comentar a parte de certificados
	}
	server = "localhost"
	port = 3306
	login = "radius"
	password = "radius"
}


# corrige o permissionamento dos arquivos de módulo
sudo chgrp -h freerad /etc/freeradius/3.0/mods-available/sql
sudo chown -R freerad:freerad /etc/freeradius/3.0/mods-enabled/sql

# reinicia o serviço do radius
sudo systemctl restart freeradius
```


### Instalar o nginx e php
```
sudo apt install -y nginx php8.1-fpm php-mysql
```


### Configura o nginx para buscar o php
```
# editar o arquivo /etc/nginx/sites-enabled/default, deixá-lo com o seguinte conteúdo:
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


### Clonar os arquivos web
```
# volta pra pasta home do usuário
cd ~

# clona o repositório com os arquivos do portal web
git clone https://github.com/sysadminbr/opnsense-hotspot

# move os arquivos pra pasta webroot do nginx
sudo mv opnsense-hotspot/radius_server/var/www/html/* /var/www/html/

# corrige permissões da webroot
sudo chown -R www-data:www-data /var/www/html/
```


### Configurar o timezone do php
```
# editar a configuração do php
sudo vim /etc/php/8.1/fpm/php.ini

# localizar e descomentar a linha date.timezone
date.timezone = America/Sao_Paulo
```

### Ajustar os dados do banco de dados
```
sudo vim /var/www/html/db.php
```

### Reiniciar os serviços
```
systemctl restart nginx php8.1-fpm
```


### OPCIONAL: instalar o certificado lets encrypt
```
sudo apt install -y certbot python3-certbot-nginx
sudo certbot -d auth.citrait.com.br
```

