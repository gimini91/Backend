FROM janrtr/docker-symfony-php7-composer

RUN apk --no-cache add php7-simplexml

ADD /app /www/symfony/app

#Add parameters.yml for Docker
ADD /docker/parameters.yml /www/symfony/app/config/parameters.yml

ADD /bin /www/symfony/bin
ADD /src /www/symfony/src
ADD /var /www/symfony/var
ADD /web /www/symfony/web
ADD /composer.json /www/symfony/composer.json
ADD /composer.lock /www/symfony/composer.lock

#Add cert volume
RUN mkdir -p /srv/lexic
RUN chown -R www:www /srv/lexic

WORKDIR /www/symfony
ENV SYMFONY_ENV=dev

RUN chown -R www:www /www

RUN composer install --no-interaction
RUN chown -R www:www /www