# 🔥 Hotspot para OPNsense com RADIUS e Portal de Autenticação

Este projeto fornece uma **solução de hotspot** para o **OPNsense**, permitindo autenticação de usuários via **RADIUS** e um **portal web** para login e auto-cadastro.

<br/> 

## ✨ **Recursos**
✅ **Autenticação via RADIUS** para integração com o OPNsense  
✅ **Portal Captive** para login via navegador  
✅ **Auto-cadastro de usuários** com validação opcional    
✅ **Interface personalizável** para a experiência do usuário  

<br/> 

## 🏗 **Arquitetura do Sistema**
📌 O sistema é baseado em três componentes principais:

1. **Servidor RADIUS (FreeRADIUS)**
   - Gerencia autenticação, autorização e contabilidade (AAA)
   - Armazena usuários e políticas de acesso


2. **Portal Web**
   - Criado em **PHP + MySQL**
   - Página de login e auto-registro
   - Integração com o FreeRADIUS para gerenciamento de usuários

3. **OPNsense (Firewall)**
   - Captive Portal ativado
   - Configuração para utilizar **RADIUS como backend de autenticação**
   - Aplicação de regras de firewall e políticas de acesso  
  
<br/> 

## 🚀 **Instalação e Configuração**
### **1️⃣ Configurar o Servidor RADIUS**
### Pré-Requisitos
* Ubuntu 24.04

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
sudo ufw allow https
sudo ufw allow ssh
```


### configurar os clientes do servidor Radius (OPNsense)
```
# cd /etc/freeradius/3.0/
# sudo vim clients.conf

client OPNsense {
        ipaddr          = 192.168.100.12
        secret          = password
}

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




### **2️⃣ Configurar o Portal Web**
### instalar o nginx e php
```
sudo apt install -y nginx php8.1-fpm php-mysql
```


### Configura o nginx para buscar o php
```
# vi /etc/nginx/sites-enabled/default
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


### baixar os arquivos do portal web
```
git clone https://github.com/sysadminbr/opnsense-hotspot
sudo mv opnsense-hotspot/radius_server/var/www/html/* /var/www/html/
sudo chown -R www-data:www-data /var/www/html/
```


### configurar o timezone do php
```
# sudo vim /etc/php/8.1/fpm/php.ini

date.timezone = America/Sao_Paulo
```

### ajustar os dados do banco de dados
```
# sudo vim /var/www/html/db.php

$db_user = "radius";
$db_pass = "radius";
$db_host = "localhost";
$db_port = 3306;
$db_name = "radius";
```

### reiniciar os serviços
```
systemctl restart nginx php8.1-fpm
```


### OPCIONAL: instalar o certificado lets encrypt
```
sudo apt install -y certbot python3-certbot-nginx
sudo certbot -d auth.sysadminbr.com.br
```


### **3️⃣ Configurar OPNsense**
1. Acesse o OPNsense via **Web GUI**
2. Vá para **System →  Access → Servers**
3. Crie um novo servidor e configure:
   - **Descriptive name**: RADIUS-HOTSPOT
   - **Hostname**: IP do Servidor RADIUS
   - **Shared Secret**: CHAVE_SECRETA
4. Vá para **Services → Captive Portal → Administration**
5. Crie uma nova zona e configure:
   - **Authentication**: RADIUS-HOTSPOT
   - **RADIUS Server**: IP do Servidor RADIUS
   - **Shared Secret**: CHAVE_SECRETA
6. Modifique o **Template do Portal** do OPNsense
   - Faça o download do template na tela de modelos.
   - Extraia o arquivo .zip
   - Substitua o arquivo index.html pelo fornecido neste projeto (arquivo templates/index.html)
   - Edite o arquivo index.html e substitua o endereço do servidor radius/web.  
   ```let target_server = 'http://192.168.100.185';```
   - Zipe a pasta do modelo e faça upload para o opnsense.
   - Edite a zona e selecione o novo template.
7. Adicione regras de firewall para permitir tráfego autenticado

<br/> 

## 🔄 **Como Funciona?**
1️⃣ O cliente se conecta ao **Wi-Fi/LAN** gerenciado pelo OPNsense  
2️⃣ Ao tentar acessar a internet, o OPNsense redireciona para o **Portal Captive**  
3️⃣ O usuário pode:
   - **Fazer login** com um usuário registrado
   - **Criar uma conta** (se permitido)  
4️⃣ O portal envia os dados para o **RADIUS Server**  
5️⃣ O RADIUS valida as credenciais e retorna permissão  
6️⃣ O OPNsense libera o tráfego do usuário para a internet  



<br/> 

### 🔹 **Aplicar Limites de Banda**
No OPNsense, configure **Traffic Shaping** para controlar o uso da rede por grupo de usuários.
  
<br/> 


## ❗ **Segurança e Boas Práticas**
✅ **Use HTTPS no portal** (Let's Encrypt + Certbot)  
✅ **Aplique regras de firewall restritivas** para isolar clientes  
✅ **Use logs para auditoria** de acessos do wi-fi  
✅ **Mantenha os softwares atualizados** para evitar vulnerabilidades  


<br/> 

## 🎯 **Próximos Passos**
🚀 **Melhorias Planejadas:**
- Suporte para **2FA (Autenticação em Dois Fatores)**
- Painel Admin para gerenciar usuários e sessões
- Dashboard para visualizar estatísticas e consultar logs

📢 **Contribuições são bem-vindas!** Faça um **fork**, envie **pull requests** ou abra **issues** com sugestões.  


<br/> 

## 💡 **Créditos e Licença**
Desenvolvido por [ludarkstar99](https://github.com/seuusuario)  
Este projeto está licenciado sob a **MIT License**. Veja [LICENSE](LICENSE) para mais detalhes.

