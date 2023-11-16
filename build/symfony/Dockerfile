FROM cirdan/sae2022-php:latest

# https://jtreminio.com/blog/running-docker-containers-as-current-host-user
# Si il y a un .env définissant USER_ID et GROUP_ID, on affecte ces valeurs à www-data pour que les fichiers
# générés par php appartiennent à l'utilisateur courant de l'host.
# On supprime www-data et on le recrée avec l'id de l'utilisateur courant.
# Une fois que c'est fait, on réaffecte la propriété des fichiers concernés depuis l'ancien www-data (33:33)
# vers le nouveau www-data.
# Tout ça n'est nécessaire que sous linux.
# Sous mac et sous Windows, Docker tourne dans une VM apparteanant déjà à l'utilisateur courant.

ARG USER_ID
ARG GROUP_ID

RUN if [ ${USER_ID:-0} -ne 0 ] && [ ${GROUP_ID:-0} -ne 0 ]; then \
    userdel -f www-data &&\
    if getent group www-data ; then groupdel www-data; fi &&\
    groupadd -g ${GROUP_ID} www-data &&\
    useradd -l -u ${USER_ID} -g www-data www-data &&\
    install -d -m 0755 -o www-data -g www-data /home/www-data &&\
    chown --changes --silent --no-dereference --recursive \
          --from=33:33 ${USER_ID}:${GROUP_ID} \
        /home/www-data \
        /run/php \
;fi

# Attention, pirouette : si USER_ID n'est pas défini, on retombe sur 0, l'id de root (dans le container), et étant \
# donné que sous MacOS, root==utilisateur courant, ça se passe bien.
# Sous Linux, on a défini USER_ID sur l'utilisateur courant via le .env, donc ça se passe bien aussi.
USER ${USER_ID:-0}
WORKDIR /var/www/html
