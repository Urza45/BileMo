<?php

namespace App\Services;

class HttpCode
{
    private static $codesHttp = [
        100 => 'Attente de la suite de la requête.',
        101 => 'Acceptation du changement de protocole.',
        102 => 'Traitement en cours.',
        103 => 'Dans l’attente de la réponse définitive',
        200 => 'OK',
        201 => 'Création d’un document.',
        202 => 'Requête traitée, mais sans garantie de résultat.',
        203 => 'Information retournée, mais générée par une source non certifiée.',
        204 => 'Requête traitée avec succès mais pas d’information à renvoyer.',
        205 => 'Requête traitée avec succès, la page courante peut être effacée.',
        206 => 'Une partie seulement de la ressource a été transmise.',
        207 => 'Réponse multiple.',
        208 => 'Le document a été envoyé précédemment dans cette collection.',
        210 => 'La copie de la ressource côté client diffère de celle du serveur (contenu ou propriétés).',
        226 => 'Le serveur a accompli la requête pour la ressource, et la réponse est une représentation du résultat d’une ou plusieurs manipulations d’instances appliquées à l’instance actuelle.',
        300 => 'L’URI demandée se rapporte à plusieurs ressources.',
        301 => 'Document déplacé de façon permanente.',
        302 => 'Document déplacé de façon temporaire.',
        303 => 'La réponse à cette requête est ailleurs.',
        304 => 'Document non modifié depuis la dernière requête.',
        305 => 'La requête doit être ré-adressée au proxy.',
        306 => 'Les requêtes suivantes doivent utiliser le proxy spécifié',
        307 => 'La requête doit être redirigée temporairement vers l’URI spécifiée sans changement de méthode.',
        308 => 'La requête doit être redirigée définitivement vers l’URI spécifiée sans changement de méthode.',
        310 => 'La requête doit être redirigée de trop nombreuses fois, ou est victime d’une boucle de redirection.',
        400 => 'La syntaxe de la requête est erronée.',
        401 => 'Une authentification est nécessaire pour accéder à la ressource.',
        402 => 'Paiement requis pour accéder à la ressource.',
        403 => 'Ressource interdite',
        404 => 'Ressource non trouvée.',
        405 => 'Méthode de requête non autorisée.',
        406 => 'La ressource demandée n’est pas disponible.',
        407 => 'Accès à la ressource autorisé par identification avec le proxy.',
        408 => 'Temps d’attente écoulé.',
        409 => 'La requête ne peut être traitée en l’état actuel.',
        410 => 'La ressource n’est plus disponible et aucune adresse de redirection n’est connue.',
        411 => 'La longueur de la requête n’a pas été précisée.',
        412 => 'Préconditions envoyées par la requête non vérifiées.',
        413 => 'Traitement abandonné dû à une requête trop importante.',
        414 => 'URI trop longue.',
        415 => 'Format de requête non supporté pour une méthode et une ressource données.',
        416 => 'Champs d’en-tête de requête « range » incorrect.',
        417 => 'Comportement attendu et défini dans l’en-tête de la requête insatisfaisante.',
        418 => '« Je suis une théière »',
        421 => 'Impossible de produire une réponse.',
        422 => 'L’entité fournie avec la requête est incompréhensible ou incomplète.',
        423 => 'L’opération ne peut avoir lieu car la ressource est verrouillée.',
        424 => 'Une méthode de la transaction a échoué.',
        425 => 'Le serveur ne peut traiter la demande car elle risque d’être rejouée.',
        426 => 'Le client devrait changer de protocole.',
        428 => 'La requête doit être conditionnelle.',
        429 => 'Trop de requêtes dans un délai donné.',
        431 => 'Les entêtes HTTP émises dépassent la taille maximale admise par le serveur.',
        449 => 'La requête devrait être renvoyée après avoir effectué une action.',
        450 => 'Bloqué par le controle parental.',
        451 => 'Ressource demandée inaccessible pour des raisons d’ordre légal.',
        456 => 'Erreur irrécupérable.',
        444 => 'Pas de réponse.',
        495 => 'Certificat SSL invalide.',
        496 => 'Certificat SSL client requis.',
        497 => 'Requête HTTP envoyée sur le port 443.',
        498 => 'Jeton expiré ou invalide.',
        499 => 'Le client a fermé la connexion avant de recevoir la réponse.',
        500 => 'Erreur interne du serveur.',
        501 => 'Fonctionnalité réclamée non supportée par le serveur.',
        502 => 'En agissant en tant que serveur proxy ou passerelle, le serveur a reçu une réponse invalide depuis le serveur distant.',
        503 => 'Service temporairement indisponible ou en maintenance.',
        504 => 'Temps d’attente d’une réponse d’un serveur à un serveur intermédiaire écoulé.',
        505 => 'Version HTTP non gérée par le serveur.',
        506 => 'Erreur de négociation.',
        507 => 'Espace insuffisant pour modifier les propriétés ou construire la collection.',
        508 => 'Boucle dans une mise en relation de ressources (RFC 584223).',
        509 => 'Dépassement de quota.',
        510 => 'La requête ne respecte pas la politique d’accès aux ressources HTTP étendues.',
        511 => 'Le client doit s’authentifier pour accéder au réseau.',
        520 => 'Erreur inconnue.',
        521 => 'Le serveur a refusé la connexion depuis Cloudflare.',
        522 => 'Cloudflare n’a pas pu négocier un TCP handshake avec le serveur d’origine.',
        523 => 'Cloudflare n’a pas réussi à joindre le serveur d’origine.',
        524 => 'Cloudflare a établi une connexion TCP avec le serveur d’origine mais n’a pas reçu de réponse HTTP avant l’expiration du délai de connexion.',
        525 => 'Cloudflare n’a pas pu négocier un SSL/TLS handshake avec le serveur d’origine.',
        526 => 'Cloudflare n’a pas pu valider le certificat SSL présenté par le serveur d’origine.',
        527 => 'Délai de connexion dépassé.'
    ];

    public static function getHttpMessage($code)
    {
        //dd(self::$codesHttp[$code]);

        if (array_key_exists((int) $code, self::$codesHttp)) {
            return self::$codesHttp[$code];
        }
        return 'Code inconnu';
    }
}
