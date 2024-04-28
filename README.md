Crypto Casino with Slots, Aviator, Crash , Dice , Flip, Jackpot, wheel, Pvp and more features.

providing setup + latest files for 100$ telegram : blancos13

Installation 

![image](https://user-images.githubusercontent.com/94198465/208200275-9d6fd2c6-0ffd-4e9d-8856-710d788830d0.png)

заменить betbase.gg на свой домен
-------------------------------------------------

apt-get update

apt-get upgrade -y

sudo apt --fix-broken install python-pycurl python-apt

sudo apt-get install software-properties-common

sudo apt install nginx

sudo add-apt-repository ppa:ondrej/php

apt-get update

apt install -y nano mc curl build-essential php7.2 php7.2-fpm git php7.2-mysql php7.2-xml php7.2-mbstring mysql-server php7.2-mysql php7.2-curl redis-server

echo "cgi.fix_pathinfo=0" » /etc/php/7.2/fpm/php.ini

service php7.2-fpm restart

-------------------------------------------------

/// Устанавливаем пароль root для mysql

sudo service mysql stop

sudo mkdir -p /var/run/mysqld

sudo chown mysql:mysql /var/run/mysqld

sudo /usr/sbin/mysqld --skip-grant-tables --skip-networking &

mysql -u root

FLUSH PRIVILEGES;

USE mysql;

ALTER USER 'root'@'localhost' IDENTIFIED BY 'blancos';


UPDATE user SET plugin="mysql_native_password" WHERE User='root';

quit

/// Мы установили пароль "11" для пользователя "root".

-------------------------------------------------

<!--Создание папки-->

mkdir -p /var/www/betbase.gg

-------------------------------------------------

<!--Установка композера-->

curl -sS https://getcomposer.org/installer | php

mv composer.phar /usr/local/bin/composer

apt -y install

-------------------------------------------------

<!--Настройка нгиникса-->


nano /etc/nginx/sites-available/betbase.gg

/// Копируем это и вставляем туда (правая кнопка мыши):

server {
listen 80;
server_name betbase.gg www.betbase.gg;
access_log /var/log/access.log;
error_log /var/log/error.log;
rewrite_log on;
root /var/www/betbase.gg/public;
index index.php;
location / {

try_files $uri $uri/ /index.php?$query_string;

}
if (!-d $request_filename) {
rewrite ^/(.+)/$ /$1 permanent;
}
location ~* \.php$ {
fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
fastcgi_index index.php;
fastcgi_split_path_info ^(.+\.php)(.*)$;
include /etc/nginx/fastcgi_params;
fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
location ~ /\.ht {
deny all;
}
location ~* \.(?:ico|css|js|jpe?g|JPG|png|svg|woff)$ {
expires 365d;
}
}

/// После того когда вставили этот код, нужно прожать ctrl + x, потом нажать Y а потом Enter

-------------------------------------------------

<!--Создаем нужные папки и удаляем ненужные-->


sudo ln -s /etc/nginx/sites-available/betbase.gg /etc/nginx/sites-enabled/


mkdir -p /var/www/betbase.gg

rm /etc/nginx/sites-available/default

-------------------------------------------------

<!--Устанавливаем права-->

chown -R www-data:www-data /var/www/betbase.gg

В /etc/nginx/nginx.conf строка 62 изменить include /etc/nginx/sites-enabled/*; на include /etc/nginx/sites-available/*;

-------------------------------------------------

<!--Перезагружаем нгиникс чтобы наши настройки сохранились-->

sudo killall apache2

service nginx restart

-------------------------------------------------

<!--Установка ноде и пм2 для дальнейшего запуска бота-->

sudo apt install nodejs

sudo apt install npm

sudo apt install build-essential

nodejs -v

npm -v

npm install forever -g

npm install forever-monitor

-------------------------------------------------

/// Первая часть закончена, теперь заливаем архив на сервер через ftp в /var/www/betbase.gg

cd /var/www/betbase.gg

unzip betbase.zip /// "betbase" название архива

/// Потом удаляем архив

-------------------------------------------------

/// Устанавливаем phpmyadmin

cd 

sudo apt install php-mbstring

sudo apt install phpmyadmin

sudo sed -i "s/|\s*\((count(\$analyzed_sql_results\['select_expr'\]\)/| (\1)/g" /usr/share/phpmyadmin/libraries/sql.lib.php

-------------------------------------------------

/// Создаем ссылку чтобы заработал phpmyadmin

ln -s /usr/share/phpmyadmin /var/www/betbase.gg/public

/// Ссылка для управления phpmyadmin "http://luckmaze.top/phpmyadmin/"

=========================СОЗДАЕМ ВСЕ ДЛЯ ЗАЛИВАНИЯ БАЗЫ И ЗАЛИВАЕМ ЕЕ=========================

/// Заходим в MySQL "Пароль создавали выше"

mysql -u root -p

show databases;

CREATE DATABASE betbase; /// создание базы, имя базы "baza"

GRANT ALL PRIVILEGES ON baza.* TO user@localhost IDENTIFIED BY '3X1i8T6bO0b4K6s9'; /// имя базы "baza", создание пользователя базы "user", пароль "3X1i8T6bO0b4K6s9"

exit

/// Заходим в phpmyadmin "http://betbase.gg/phpmyadmin/" или adminer и заливаем бд.

=========================СОЗДАЕМ ВСЕ ДЛЯ ЗАЛИВАНИЯ БАЗЫ И ЗАЛИВАЕМ ЕЕ END=========================

/// После прописываем данные от базы в ".env" находится он в "/var/www/betbase.gg/.env"
/// Теперь выдаем права на папку:

chmod -Rf 777 /var/www/betbase.gg/storage

-------------------------------------------------

/// Установка SSL на NGINX

sudo add-apt-repository ppa:certbot/certbot

sudo apt install python-certbot-nginx

sudo apt install certbot python3-certbot-nginx

sudo ufw allow 'Nginx Full'

sudo ufw delete allow 'Nginx HTTP'

sudo certbot --nginx -d betbase.gg -d www.betbase.gg

/// Select the appropriate number [1-2] then [enter] (press 'c' to cancel): ЖМЕМ 2

-------------------------------------------------

cd /var/www/betbase.gg/server

sudo npm install pm2@latest -g

pm2 start app.js
\
