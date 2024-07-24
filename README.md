## Sekkeh Installation Tips
sekkeh is a payment microservice

### Requirements installation

Install web server (nginx) <br>
Install php > 7.4  last stable version<br>
Install mariadb last stable version<br>
Install phpmyadmin -last stable version<br>
Install the composer v2<br>
Enable the event on mariadb<br>
Install soap client<br>
Install curl<br>
Set valid ip on the server<br>
Set domain to valid ip `sekkeh.filmgardi.com`<br>
Install ssl<br>
### Requirements Command
run `git clone https://github.com/meysamzandy/sekkeh-laravel-payment-microservice.git` <br>
run `cd /var/www/sekkeh` <br>
run `composer install` <br>
Duplicate .env.example to .env <br>
configure mysql connection on .env <br>
run `php artisan key:generate` <br>
run `php artisan migrate` <br>

