

Tirage au sort
- Joueur connecté dans l'heure précédente
- Juste envoi d'un SMS (si clic par l'admin), message en dur


TODO
- Activation ON/OFF service mail
- Activation ON/OFF service sms
- Tirage au sort global (tirages entrées gratuites)
NON - Envoi SMS masse salon
- Phases de jeu : attente, tirage entrées gratuites, tirages salon, fin de jeu




APP_ENV=prod APP_DEBUG=0 php7 bin/console cache:clear

php7 bin/console doctrine:database:create
php7 bin/console doctrine:schema:create


setfacl -R -m  u:www-data:rwX -m u:raphael:rwX var
setfacl -dR -m u:www-data:rwX -m u:raphael:rwX var


https://eu.api.ovh.com/createToken/
