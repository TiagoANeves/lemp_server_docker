# LEMP Server - Docker Compose - CentOS 7

The LEMP Server consists of the following software:

  - Linux
  - Nginx
  - PHP-FPM
  - MariaBD

# Installing

Clone this repository:

```sh
git clone https://github.com/TiagoANeves/lemp_server_docker.git
```
Run the following commands to update and install the necessary packages on your linux machine:

```sh
yum update -y
yum install epel-release vim -y
yum install docker docker-compose -y
systemctl start docker
systemctl enable docker
```
Open port 80 e 443 on firewalld:
```sh
firewall-cmd --permanent --add-service=http
firewall-cmd --permanent --add-service=https
firewall-cmd --reload
```

Create the .env file and set the root password for the data base inside the file:

```
cp env-example .env
```


#### Configure you domain
Edit the following files and replace domain.com with your own domain:
 - nginx-conf/lemp.conf
 - nginx-conf/lemp.conf.ssl.bkp
 - docker-compose.yml

#### Getting SSL certificate by Let's Encrypt

Run this commando to start to containers
```ssh
docker-compose up -d
```
The result should be like this:

```
Creating mariadb ... done
Creating php-fpm ... done
Creating webserver ... done
Creating certbot ... done
```

Check if the certificate was created inside webserver container

```ssh
docker-compose exec webserver ls -la /etc/letsencrypt/live
```
The result should be like this:
```
[root@web website]# docker-compose exec webserver ls -la /etc/letsencrypt/live
total 4
drwx------. 3 root root  51 Jun  6 21:24 .
drwxr-xr-x. 9 root root 108 Jun  6 21:24 ..
-rw-r--r--. 1 root root 740 Jun  6 21:24 README
drwxr-xr-x. 2 root root  93 Jun  6 21:24 domain.com
```
Edit the docker-compose.yml file and comment the first parameter "command" of certbot configuration and uncomment the second one:
```
   # command: certonly --webroot --webroot-path=/var/www/html --email contato@domain.com --agree-tos --no-eff-email --staging -d domain.com -d www.domain.com
    command: certonly --webroot --webroot-path=/var/www/html --email contato@domain.com --agree-tos --no-eff-email --force-renewal -d domain.com -d www.domain.com
```

Run the following command to recreate the certbot container:

```
docker-compose up --force-recreate --no-deps certbot
```

The output should be someting lke this:

```
[root@web website]# docker-compose up --force-recreate --no-deps certbot
Recreating certbot ... done
Attaching to certbot
certbot      | Saving debug log to /var/log/letsencrypt/letsencrypt.log
certbot      | Plugins selected: Authenticator webroot, Installer None
certbot      | Renewing an existing certificate
certbot      | Performing the following challenges:
certbot      | http-01 challenge for domamin.com
certbot      | Using the webroot path /var/www/html for all unmatched domains.
certbot      | Waiting for verification...
certbot      | Cleaning up challenges
certbot      | IMPORTANT NOTES:
certbot      |  - Congratulations! Your certificate and chain have been saved at:
certbot      |    /etc/letsencrypt/live/domamin.com/fullchain.pem
certbot      |    Your key file has been saved at:
certbot      |    /etc/letsencrypt/live/domamin.com/privkey.pem
certbot      |    Your cert will expire on 2020-09-04. To obtain a new or tweaked
certbot      |    version of this certificate in the future, simply run certbot
certbot      |    again. To non-interactively renew *all* of your certificates, run
certbot      |    "certbot renew"
certbot      |  - Your account credentials have been saved in your Certbot
certbot      |    configuration directory at /etc/letsencrypt. You should make a
certbot      |    secure backup of this folder now. This configuration directory will
certbot      |    also contain certificates and private keys obtained by Certbot so
certbot      |    making regular backups of this folder is ideal.
certbot      |  - If you like Certbot, please consider supporting our work by:
certbot      |
certbot      |    Donating to ISRG / Let's Encrypt:   https://letsencrypt.org/donate
certbot      |    Donating to EFF:                    https://eff.org/donate-le
certbot      |
certbot exited with code 0
```

#### Changing the Nginx configuration

Stop the webserver container:

```
docker-compose stop webserver
```

Replace the nginx-conf/lemp.conf with the nginx-conf/lemp.conf.ssl.bkp 
Remember to replace the domain.com with your own domain in this file!

```ssh
cp nginx-conf/lemp.conf.ssl.bkp nginx-conf/lemp.conf
```

Edite the docker-compose.yml file and uncomment the port 443 on webserver

```
    ports:
      - "80:80"
      - "443:443"
```

Start the webserver container:

```ssh
docker-compose up -d --force-recreate --no-deps webserver
```

Open your domain in the browser and check if the PHP Info page will load. 
Now your Nginx and PHP-FPM are running with SSL certificate!!

#### Testing the MariaDB with Ngind and PHP-FPM

Access the mariadb container:

```
docker exec -it mariadb bash
```

Run the following commands to create a new database and a user to use it. The mysql -u root -p command wil ask for a password, its the same that you set in the .env file.

```
mysql -u root -p
MariaDB [(none)]> create database test;
MariaDB [(none)]> GRANT ALL PRIVILEGES ON test.* to 'test'@'webserver' IDENTIFIED BY 'dbpassword';
MariaDB [(none)]> flush privileges;
MariaDB [(none)]> exit;
```

Open the db.php on your browser: domain.com/db.php

You will see the message: Success!!!

Now you are done! All services are running!!

That's all folks!

## License

lemp_server_docker is licensed under the GNU GPL license. take a look at the [LICENSE](https://github.com/TiagoANeves/lemp_server_docker/blob/master/LICENSE) for more information.

## Version
**Current version is 1.0.0**

## Credits
- [Tiago Neves](https://github.com/TiagoANeves)
