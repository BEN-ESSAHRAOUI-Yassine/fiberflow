# GraceTHD MCD Schema Reference

> Généré depuis `Grilles_PRO_EXE_REC_T_D_V4_20210310.xlsx - MCD_Attributs.tsv`
> Phase matrix: **O** = Obligatoire, **C** = Conditionnel, **N** = Non nécessaire

## Tables (32)

- `t_adresse`
- `t_baie`
- `t_cab_cond`
- `t_cable`
- `t_cable_patch201`
- `t_cableline`
- `t_cassette`
- `t_cassette_patch201`
- `t_cheminement`
- `t_cond_chem`
- `t_conduite`
- `t_ebp`
- `t_fibre`
- `t_love`
- `t_ltech`
- `t_ltech_patch201`
- `t_masque`
- `t_noeud`
- `t_organisme`
- `t_position`
- `t_ptech`
- `t_reference`
- `t_ropt`
- `t_sitetech`
- `t_suf`
- `t_tiroir`
- `t_zcoax`
- `t_zdep`
- `t_znro`
- `t_zpbo`
- `t_zpbo_patch201`
- `t_zsro`

---

## `t_adresse`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `ad_code` | `VARCHAR (254)` | Code unique de l'adresse. | O | O | O | N | N | O | O |  |
| `ad_ban_id` | `VARCHAR (24)` | Identifiant Base Adresse Nationale | N | N | N | N | N | N | N |  |
| `ad_nomvoie` | `VARCHAR (254)` | Nom de la voie | C | C | C | N | N | C | C |  Lettres en majuscule uniquement. Il ne faut ni accent, ni apostrophe, aucune cédille, aucun caractère spécial.  |
| `ad_fantoir` | `VARCHAR (10)` | Identifiant  FANTOIR  contenu  dans  le fichier des propriétés bâtis de la DGFiP | N | N | N | N | N | N | N |  |
| `ad_numero` | `INTEGER` | Numéro  éventuel  de  l’adresse  dans  la voie | C | C | C | N | N | C | C | C dans le cas où il n'y a pas de numéro |
| `ad_rep` | `VARCHAR (20)` | Indice de répétition associé au numéro (par exemple Bis, A, 1…) | C | C | C | N | N | C | C |  Lettres en majuscule uniquement |
| `ad_insee` | `VARCHAR(6)` | Identifiant INSEE de la commune fondé sur le COG en cours | O | O | O | N | N | O | O |  |
| `ad_postal` | `VARCHAR(20)` | Code  postal  du  bureau  de  distribution de la voie | O | O | O | N | N | O | O |  |
| `ad_alias` | `VARCHAR(254)` | Eventuellement   le   nom    en   langue régionale   ou   une   autre   appellation différente de l’appellation officielle | N | N | N | N | N | N | N |  |
| `ad_nom_ld` | `VARCHAR(254)` | Nom  du  lieu-dit  qui  peut  être  le  nom de la voie parfois | C | C | C | N | N | C | C | A renseigner si si "ad_nomvoie" est vide .  |
| `ad_x_ban` | `NUMERIC` | X en lambert 93 | N | N | N | N | N | N | N |  |
| `ad_y_ban` | `NUMERIC` | Y en lambert 93 | N | N | N | N | N | N | N |  |
| `ad_commune` | `VARCHAR (254)` | Nom officiel de la commune | O | O | O | N | N | O | O | Lettres en majuscule uniquement. Il ne faut ni accent, ni apostrophe, aucune cédille, aucun caractère spécial. S'il y a des tirets, pas d'espace entre les lettres et les tirets  |
| `ad_section` | `VARCHAR (5)` | Section cadastrale pour ceux qui souhaitent utiliser les numéros de parcelles du PCI. | N | N | N | N | N | N | N |  |
| `ad_idpar` | `VARCHAR (20)` | Identifiant de la parcelle de référence. Notion base MAJIC. | N | N | N | N | N | N | N |  |
| `ad_x_parc` | `NUMERIC` | X en lambert 93 de la parcelle identifiée comme parcelle de référence (base MAJICIII quand disponible). | N | N | N | N | N | N | N |  |
| `ad_y_parc` | `NUMERIC` | Y en lambert 93 de la parcelle identifiée comme parcelle de référence (base MAJICIII quand disponible). | N | N | N | N | N | N | N |  |
| `ad_nat` | `BOOLEAN` | Oui si le site n'est pas une propriété privée. | N | N | N | N | N | N | N |  |
| `ad_nblhab` | `INTEGER` | Nombre de locaux d'habitation (foyers). | N | N | N | N | N | N | N |  |
| `ad_nblpro` | `INTEGER` | Nombre de locaux professionnels. | N | N | N | N | N | N | N |  |
| `ad_nbprhab` | `INTEGER` | Nombre de prises habitation. | O | O | O | N | N | O | O | tous les accès résidentiels indiqués dans les règles de dénombrement  |
| `ad_nbprpro` | `INTEGER` | Nombre de prises professionnelles | O | O | O | N | N | O | O | tous les accès Pro indiqués dans les règles de dénombrement  |
| `ad_rivoli` | `VARCHAR (254)` | Code RIVOLI (source Orange) exploité par certains opérateurs. | N | N | N | N | N | N | N |  |
| `ad_hexacle` | `VARCHAR (254)` | Code HEXACLE | N | C | C | N | N | C | C | C pour MOA et fermier s'il est existant |
| `ad_hexaclv` | `VARCHAR (254)` | Code HEXACLE Voie. Correspond au 0 de la voie. Est différent de l'Hexavia. La bonne pratiqque est de le renseigner s'il existe et particulierement en l'absence d'hexaclé | N | C | C | N | N | C | C | C pour MOA et fermier s'il est existant |
| `ad_distinf` | `NUMERIC` | Distance en mètres de l'infra mobilisable en distribution. (calculable) | N | O | O | N | N | O | O | Distance à vol d'oiseau sans marge entre le PB et le centre du bâtiment.  |
| `ad_isole` | `BOOLEAN` | Pour distinguer les SUF considérés comme isolés (distance supérieure au maximum contractuel) – calculable. | N | N | N | N | N | N | N | Si nécessaire pour Orange, le fermier le rempli à "O" si ad_distinf > 150 mètres |
| `ad_prio` | `BOOLEAN` | Le raccordement du site est-il prioritaire ? | N | N | N | N | N | N | N |  |
| `ad_racc` | `VARCHAR(2)` (`REFERENCES l_implantation_type(code)`) | Type de raccordement du site | N | O | O | N | N | O | O | A renseigner pour les sites clients . Obligatoires pour ttes les adresses dès lors qu'elles concernent un SUF rattaché a un PBE (PB extérieur). Le choix de la valeur dans la liste est fonction de la contrainte de raccordement la plus elevée sur le parcours PB-PTO. Pour les valeurs concernant l'aérien ou le souterrain, aucune distinction entre le GC OF et GC collectivité n'est à faire.   Classement des contraintes de la plus forte à la plus faible à respecter par l'intégrateur. Voir liste ci-dessous.  Pour les cas "aéro-souterrain", il faut utilisé la contrainte aérienne la plus forte (aérien énergie ou aérien Telecom)  Renseigner la contrainte la plus forte (1 AERIEN ENERGIE) pour les poteaux non identifiés des RBAL déjà effectués.   1 AERIEN ENERGIE  -->  si un appui « ENEDIS » est sur le parcours PB/IMB alors le délégant choisit la valeur aérien énergie systématiquement qq soit les autres composantes du parcours 0 AERIEN TELECOM  --> si un appui Telecom ou potelet est sur le parcours PB/IMB sans appui ENEDIS » alors le délégant choisit la valeur aérien télécom  qq soit les autres composantes du parcours 2 FACADE -->  si un élément réseau du parcours horizontal PB/IMB est en façade sans appui « ENEDIS » ni appui télécom alors le délégant choisit la valeur façade  qq soit les autres composantes du parcours 4 PLEINE TERRE  -->  si un élément réseau du parcours PB/IMB est en pleine terre sans appui « ENEDIS » ni appui télécom ni élément en façade alors le délégant choisit la valeur pleine terre qq soit les autres composantes du parcours 8 EGOUT --> si le parcours réseau PB/IMB ne comporte ni un appui « ENEDSI », ni appui télécom, ni élément en façade, ni un élément en pleine terre mais passe par un parcours GC souterrain avec contrainte GC alors le délégant choisit au choix les valeurs egouts, ou caniveau ou galerie 6 GALERIE  -->  si le parcours réseau PB/IMB ne comporte ni un appui « ENEDSI », ni appui télécom, ni élément en façade, ni un élément en pleine terre mais passe par un parcours GC souterrain avec contrainte GC alors le délégant choisit au choix les valeurs egouts, ou caniveau ou galerie 5 CANIVEAU -->  si le parcours réseau PB/IMB ne comporte ni un appui « ENERGIE », ni appui télécom, ni élément en façade, ni un élément en pleine terre mais passe par un parcours GC souterrain avec contrainte GC alors le délégant choisit au choix les valeurs egouts, ou caniveau ou galerie 7 CONDUITE  --> si le parcours réseau PB/IMB ne comporte qu’un parcours en conduite sans contrainte particulière de GC alors le délégant choisit la valeur  conduite 3 IMMEUBLE  Vide --> NP |
| `ad_batcode` | `VARCHAR(100)` | Identifiant du bâtiment dans une base de données externe (IGN, OSM, DGFiP, etc.). | N | N | N | N | N | N | N | Le fermier renseignera en fonction de ad_code qui sera fixe.  |
| `ad_nombat` | `VARCHAR(254)` | Ce champ correspond au nom du batiment tel que décrit par l'opérateur d'immeuble en cohérence avec ce qu'il constate sur le terrain. Ce champ peut apparaitre après la publication de l'adresse dans l'IPE car fiabilisé au cours de la phase de piquetage terrain. | C | C | C | N | N | C | C | Règles de gestion : Dans le cas de doublons d'adresse (plusieurs constructions à la meme adresse), saisir le nom du batiment avec    > un nom ou numero s'il est indiqué sur la facade/parvis    > le cas échéant, on met Bat 1, Bat 2, Bat3…. Lettres en majuscule uniquement. Il ne faut ni accent, ni apostrophe, aucune cédille, aucun caractère spécial. S'il y a des tirets, pas d'espace entre les lettres et les tirets  |
| `ad_ietat` | `VARCHAR(2)` (`REFERENCES l_adresse_etat(code)`) | Permet d'indiquer l'avancement du déploiement. (IPE O) | O | O | O | N | N | O | O |  Devra être à "ciblé" pour la consultation de lot et à "en cours de déploiement"  pour le REC  |
| `ad_itypeim` | `VARCHAR (1)` (`REFERENCES l_immeuble_type(code)`) | Type d'immeuble (IPE O). | O | O | O | N | N | O | O | Saisir "I" quand il existe une partie commune pour au moins 4 prises (somme des prises de type habitation et professionnel) Saisir P dans le cas contraire. |
| `ad_imneuf` | `BOOLEAN` | Ce champ permet d'indiquer s'il s'agit d'un habitat collectif en cours de construction pendant le déploiement du PM qui le dessert, qu'il s'agisse d'un PMI ou d'un PME. (IPE F) | N | N | O | N | N | O | O | O si immeuble préfibré. Sinon N. (Peu importe l'année de l'immeuble) |
| `ad_idatimn` | `DATE` | Ce champ est utilisé dans le cadre des immeubles neufs et facultatif. Il permet à l'opérateur d'immeuble d'indiquer la date prévisionnelle de livraison de l'immeuble indiquée par le constructeur de l'immeuble. Cette date constitue une tendance sans garantie de mise à jour par l'opérateur d'immeuble. (IPE F) | N | N | C | N | N | C | C | = date de permis de construire. A renseigner obligatoirement si la date de création de l'immeuble est > au 01/07/97 car dans ce cas le Diagnostic Amiante n'est pas requis.  |
| `ad_prop` | `VARCHAR (254)` | Identifiant du propriétaire de l'immeuble (entreprise ou personne) dans le référentiel des propriétaires. | N | N | N | N | N | N | N |  |
| `ad_gest` | `VARCHAR (20)` | Identifiant du gestionnaire d'immeuble (entreprise ou personne) dans le référentiel des gestionnaires. (IPE C) | N | N | C | N | N | C | C | sera fourni à la phase PRO distri avec or_code de la personne avec qui on a signé la convention immeuble |
| `ad_idatsgn` | `DATE` | Date de la signature de la convention avec le gestionnaire de l'immeuble. (IPE C) | N | N | C | N | N | C | C | Date de signature de la convention syndic avec le gestionnaire immeuble  |
| `ad_iaccgst` | `BOOLEAN` | Permet de savoir si un accord du gestionnaire d'immeuble (copropriété, syndic, etc.) est nécessaire ou non pour aller raccorder l'adresse. (Obligatoire IPE) | N | N | N | N | N | N | N |  |
| `ad_idatcab` | `DATE` | Date prévisionnelle ou effective du câblage de l'adresse c'est à dire de déploiement de l'adresse. Cette date correspond à la date à laquelle EtatImmeuble passera à l'état déployé et l'adresse sera raccordable. (obligatoire IPE) | N | N | N | N | N | N | N |  |
| `ad_idatcom` | `DATE` | Ce champ correspond à la date à laquelle le raccordement effectif d'un client final à cet immeuble est possible du point de vue de la réglementation. Il correspond à la date d'ouverture à la commercialisation d'une ligne. (IPE F) | N | N | N | N | N | N | N |  |
| `ad_typzone` | `VARCHAR (1)` (`REFERENCES l_zone_densite(code)`) | Type de zone de l'adresse desservie. (IPE O) | N | N | N | N | N | N | N |  |
| `ad_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `ad_geolqlt` | `NUMERIC(6,2)` | Précision du positionnement de l'objet, estimée en mètres. La précision doit être déduite du mode d'implantation et du support d'implantation, en tenant compte selon les cas du cumul des imprécisions : des levés ou du fond de plan (utiliser dans ce cas la classe de précision planimétrique au sens de l'arrêté du 16 septembre 2003), de l'outil de détection, des cotations, de l'éventuel report 'à main levée', etc. | N | N | N | N | N | N | N |  |
| `ad_geolmod` | `VARCHAR(4)` (`REFERENCES l_geoloc_mode(code)`) | Mode d'implantation de l'objet. | N | N | N | N | N | N | N |  |
| `ad_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `ad_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | N | N | O | O | date de création initiale de l'ID Grace par le BE, l'ETR ou Exploitant |
| `ad_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | N | N | N | N | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `ad_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | C | C | A renseigner, si une mise à jour a été effectuée.  |
| `ad_abddate` | `DATE` | Date d'abandon de l'objet | C | N | N | N | N | C | C | Si objet abandoné, mettre la date d'abandon |
| `ad_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | C | C | A renseigner, si l'objet a été abandonné.  |
| `geom` | `Geometry(Point,2154)` | Point abstrait | O | O | O | N | N | O | O | Correspond au centre du bâtiment  |

---

## `t_baie`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `ba_code` | `VARCHAR(254)` | Code baie ou ferme | O | O | O | O | O | O | O |  |
| `ba_codeext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | N | O | O | O | O | O | O | Référence règles de nommage des éléments FTTH remise à chaque bureau d'étude  Prend les valeurs "Gauche" ou "Droite" |
| `ba_etiquet` | `VARCHAR(254)` | Etiquette sur le terrain | N | N | N | N | N | N | N |  |
| `ba_lt_code` | `VARCHAR(254)` (`REFERENCES t_ltech (lt_code)`) | Code du local technique | N | O | O | O | O | O | O |  |
| `ba_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du propriétaire du tiroir. | N | N | N | N | N | N | N |  |
| `ba_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant unique du gestionnaire. | N | N | N | N | N | N | N |  |
| `ba_user` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Utilisateur | N | N | N | N | N | N | N |  |
| `ba_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `ba_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Identifiant unique du statut de déploiement. | N | N | N | N | N | N | N |  |
| `ba_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat de la BAIE | N | N | N | N | N | N | N |  |
| `ba_rf_code` | `VARCHAR(254)` (`REFERENCES t_reference (rf_code)`) | Identifiant de la référence de la baie dans la table référence. | N | N | O | O | O | O | O |  |
| `ba_type` | `VARCHAR(10)` (`REFERENCES l_baie_type (code)`) | Type du contenant selon qu'il s'agisse d'une BAIE ou d'une FERME. Voir liste de choix | N | O | O | O | O | O | O | Toujours "BAIE" |
| `ba_nb_u` | `NUMERIC` | Taille de la baie en nombre de U | N | N | N | N | N | N | N |  |
| `ba_haut` | `NUMERIC` | Hauteur en mm | N | N | N | N | N | N | N |  |
| `ba_larg` | `NUMERIC` | Largeur en mm | N | N | N | N | N | N | N |  |
| `ba_prof` | `NUMERIC` | Profondeur en mm | N | N | N | N | N | N | N |  |
| `ba_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `ba_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | O | O | O | O |  |
| `ba_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `ba_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `ba_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `ba_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_cab_cond`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cc_cb_code` | `VARCHAR(254)` (`REFERENCES t_cable(cb_code)`) | Code câble | N | O | O | O | O | O | O |  |
| `cc_cd_code` | `VARCHAR(254)` (`REFERENCES t_conduite(cd_code)`) | Code d'une conduite accueillant le câble. | N | O | O | O | O | O | O |  |
| `cc_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `cc_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `cc_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `cc_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `cc_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_cable`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cb_code` | `VARCHAR(254)` | Code câble | N | O | O | O | O | O | O |  |
| `cb_codeext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | N | N | O | O | O | O | O | Référence règles de nommage des éléments FTTH remise à chaque bureau d'étude La règle générale de nommage pour les câbles (transport et distribution) est la suivante : INSEE/NRO/TR AA XXXX (Attention, il ne doit pas y avoir d’espace avant et après les / et il doit y avoir un espace après TR et après AA) avec :         - INSEE => 5 caractères  (code INSEE de la commune d’implantation du NRO)         - NRO => 3 caractères trigramme NRO interne de l’Exploitant qui est différent de la référence du projet (voir fichier annexe 1)        -  AA => 2 digits d’identification du créateur       - XXXX => 4 digits d’incrémentation o démarrage à 01 pour le premier élément rencontré de l’étude   |
| `cb_etiquet` | `VARCHAR(254)` | Etiquette sur le terrain | N | N | O | O | O | O | O | Pour les câbles, nommage à respecter sur les étiquettes terrain :  NRO/TR AA XXXX  issu du "cb_codext" Le propriétaire, le type de réseau, la capacité en FO, le n° de commande FCI et la date de pose seront ajoutés ==> voir règles d'étiquetage  |
| `cb_nd1` | `VARCHAR(254)` (`REFERENCES t_noeud(nd_code)`) | Code du noeud à l'extrêmité 1 du câble. Pour un cable intrasite (jarretière, etc.) cb_nd1 et cb_nd2 seront identiques. | N | O | O | O | O | O | O |  |
| `cb_nd2` | `VARCHAR(254)` (`REFERENCES t_noeud(nd_code)`) | Code du noeud à l'extrêmité 2 du câble. Pour un cable intrasite (jarretière, etc.) cb_nd1 et cb_nd2 seront identiques. | N | O | O | O | O | O | O |  |
| `cb_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | N | O | O | O | O | O | O | DP + n° dpt sur 2 digits (ex : DP71) |
| `cb_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | N | O | O | O | O | O | O | Code NRO du département (ex : NRO39086TNI) |
| `cb_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | O | O | O | O | O | O | Code SRO (ex : NRO39086TNI_11) si le câble concerne une ZSRO Code Transport  (ex: NRO71512SGI_B2) si le câble concerne une branche de Transport |
| `cb_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `cb_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Propriétaire du câble | N | O | O | O | O | O | O | Sera toujours la collectivité |
| `cb_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Gestionnaire du câble | N | O | O | O | O | O | O | Sera toujours l'exploitant |
| `cb_user` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Utilisateur du câble | N | N | N | N | N | N | N |  |
| `cb_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `cb_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | N | O | O | O | O | O | O | PRO, EXE ou REC |
| `cb_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat du câble | N | N | N | N | N | N | N |  |
| `cb_dateins` | `DATE` | Date de pose du câble | N | N | N | N | N | N | N |  |
| `cb_datemes` | `DATE` | Date de mise en service | N | N | N | N | N | N | N |  |
| `cb_avct` | `VARCHAR(1)` (`REFERENCES l_avancement(code)`) | Attribut synthétisant l'avancement. Utile pour distinguer en phase d'étude ce qui est existant et à créer. Usage conditionnel. | N | N | N | N | O | O | O | NOK à l'EXE EXISTANT si câble installé et fonctionnel.  Sinon, A CREER ou TRAVAUX.  Bien de retour suite MAD : EN SERVICE.  |
| `cb_tech` | `VARCHAR(3)` (`REFERENCES l_technologie_type (code)`) | Technologie du câble (fibre optique, cuivre, coaxial, etc.) | N | N | N | N | N | N | N |  |
| `cb_typephy` | `VARCHAR(1)` (`REFERENCES l_cable_type (code)`) | Type physique du câble. | N | N | N | N | N | N | N |  |
| `cb_typelog` | `VARCHAR(2)` (`REFERENCES l_infra_type_log (code)`) | Type logique du câble (collecte, transport, distribution, etc.). | N | O | O | O | O | O | O |  |
| `cb_rf_code` | `VARCHAR(254)` (`REFERENCES t_reference (rf_code)`) | Identifiant de la référence du câble dans la table référence. | N | N | O | O | O | O | O |  |
| `cb_capafo` | `INTEGER` | Capacité du câble (Nombre total de fibres présentes). | N | O | O | O | O | O | O |  |
| `cb_fo_disp` | `INTEGER` | Nombre de fibres présentes dans le câble et encore disponibles (différence entre le nombre total de fibres et le nombre de fibres utilisées) | N | N | N | N | N | N | N | Ne pas saisir tant que la règle de gestion n'est pas clairement définie côté AVICCA.  |
| `cb_fo_util` | `INTEGER` | Nombre de fibres utiles sur le segment d'infrastructure pour desservir les SUF situés en aval (incluant les besoins de l'infrastructure d'imbrication), corrigé en fonction de la localisation et du dénombrement des Sites Utilisateurs Finaux après relevé terrain. | N | N | N | N | N | N | N | Ne pas saisir tant que la règle de gestion n'est pas clairement définie côté AVICCA.  |
| `cb_modulo` | `INTEGER` | Nombre de fibres par tube (6, 12) | N | O | O | O | O | O | O |  |
| `cb_diam` | `NUMERIC` | Diamètre du câble en millimètres | N | N | N | N | N | N | N |  |
| `cb_color` | `VARCHAR(254)` | Couleur du câble | N | N | N | N | N | N | N |  |
| `cb_lgreel` | `NUMERIC` | Longueur réelle du câble en mètres (selon retours terrain) | N | N | O | O | O | O | O |  |
| `cb_localis` | `VARCHAR(254)` | Localisation du câble lorsqu'il s'agit d'un cablage intrasite. Ceci peut-être utile lorsque la fibre n'est pas modélisée. Il peut s'agir d'une indication littérale, ou du code d'un tiroir, du code d'un EBP, etc. | N | N | N | N | N | N | N |  |
| `cb_comment` | `VARCHAR(254)` | commentaire | N | N | N | N | N | N | N |  |
| `cb_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `cb_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `cb_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `cb_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `cb_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_cable_patch201`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cb_code` | `VARCHAR(254)` (`REFERENCES t_cable (cb_code)`) | Code unique du Câble | N | N | N | N | N | N | N |  |
| `cb_bp1` | `VARCHAR(254)` (`REFERENCES t_ebp(bp_code)`) | EBP de départ du câble | N | N | N | N | N | N | N |  |
| `cb_ba1` | `VARCHAR(254)` (`REFERENCES t_baie(ba_code)`) | Baie de départ du câble En cas d'éclatement sur plusieurs baies, saisir la baie principale | N | N | N | N | N | N | N |  |
| `cb_bp2` | `VARCHAR(254)` (`REFERENCES t_ebp(bp_code)`) | EBP d'arrivée du câble | N | N | N | N | N | N | N |  |
| `cb_ba2` | `VARCHAR(254)` (`REFERENCES t_baie(ba_code)`) | Baie d'arrivée du câble En cas d'éclatement sur plusieurs baies, saisir la baie principale | N | N | N | N | N | N | N |  |

---

## `t_cableline`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cl_code` | `VARCHAR(254)` | Code unique permettant d'identifier une géométrie modélisant un câble. | N | O | O | O | O | O | O |  |
| `cl_cb_code` | `VARCHAR (254)` (`REFERENCES t_cable(cb_code)`) | Code unique du câble tel que saisi dans cb_code. | N | O | O | O | O | O | O |  |
| `cl_long` | `NUMERIC` | Longueur totale du câble (hérité de la géométrie) | N | N | N | N | N | N | N |  |
| `cl_comment` | `VARCHAR(254)` | commentaire | N | N | N | N | N | N | N |  |
| `cl_dtclass` | `VARCHAR(2)` (`REFERENCES l_geoloc_classe(code)`) | Classe de précision au sens du décret DT-DICT | N | N | N | N | N | N | N |  |
| `cl_geolqlt` | `NUMERIC(6,2)` | Précision du positionnement de l'objet, estimée en mètres. La précision doit être déduite du mode d'implantation et du support d'implantation, en tenant compte selon les cas du cumul des imprécisions : des levés ou du fond de plan (utiliser dans ce cas la classe de précision planimétrique au sens de l'arrêté du 16 septembre 2003), de l'outil de détection, des cotations, de l'éventuel report 'à main levée', etc. | N | N | N | N | N | N | N |  |
| `cl_geolmod` | `VARCHAR(4)` (`REFERENCES l_geoloc_mode(code)`) | Mode d'implantation de l'objet. | N | N | N | N | N | N | N |  |
| `cl_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `cl_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `cl_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `cl_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `geom` | `Geometry(Linestring,2154)` | Ligne | N | O | O | O | O | O | O |  |
| `cl_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `cl_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_cassette`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cs_code` | `VARCHAR(254)` | Code unique de la cassette. | N | N | O | O | O | O | O |  |
| `cs_nb_pas` | `INTEGER` | Taille de la cassette lorsqu'elle est placée dans un BPE (en nombre de pas) | N | N | N | N | O | O | O |  |
| `cs_bp_code` | `VARCHAR(254)` (`REFERENCES t_ebp (bp_code)`) | Identifiant unique du BPE à laquelle appartient la cassette | N | N | C | C | C | C | C | Doit être renseigné lorsque la cassette se trouve dans un EBP |
| `cs_num` | `INTEGER` | Numéro de la cassette dans l'organiseur de la BPE. | N | N | O | O | O | O | O | Dans un BPE : ‘’0’’ est réservé à la cassette de stockage. Le numéro de la cassette est le numéro du pas dans lequel est installée la cassette dans l’organisateur. Dans un NRO ou un SRO : On compte les cassettes de la tête ou du tiroir optique de haut en bas.   |
| `cs_type` | `VARCHAR(1)` (`REFERENCES l_cassette_type (code)`) | Type de cassette (SOUDURE, LOVAGE, SPLITTER, CONNECTEUR, …) | N | N | N | N | N | N | N |  |
| `cs_face` | `VARCHAR(20)` | Face du BPE sur laquelle est enfichée la cassette (défaut = Face A) | N | N | N | N | N | N | N |  |
| `cs_rf_code` | `VARCHAR(254)` (`REFERENCES t_reference (rf_code)`) | Identifiant unique dans la table référence. | N | N | N | N | O | O | O | NOK à l'EXE |
| `cs_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `cs_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | O | O | O | O | O |  |
| `cs_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `cs_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `cs_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `cs_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_cassette_patch201`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cs_code` | `VARCHAR(254)` (`REFERENCES t_cassette(cs_code)`) | Code unique de la cassette | N | N | N | N | N | N | N |  |
| `cs_ti_code` | `VARCHAR(254)` (`REFERENCES t_tiroir(ti_code)`) | Code unique du tiroir | N | N | N | N | N | N | N |  |

---

## `t_cheminement`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cm_code` | `VARCHAR(254)` | Code du cheminement. | N | O | O | O | O | O | O |  |
| `cm_codeext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | N | N | N | N | N | N | N |  |
| `cm_ndcode1` | `VARCHAR(254)` (`REFERENCES t_noeud(nd_code)`) | Code du Noeud à une extrêmité de la séquence de cheminements. | N | O | O | O | O | O | O |  |
| `cm_ndcode2` | `VARCHAR(254)` (`REFERENCES t_noeud(nd_code)`) | Code du Noeud à l'autre extrêmité de la séquence de cheminements. | N | O | O | O | O | O | O |  |
| `cm_cm1` | `VARCHAR(254)` | Code du cheminement à une extrêmité (déductible de la géométrie). | N | N | N | N | N | N | N |  |
| `cm_cm2` | `VARCHAR(254)` | Code du cheminement à l'autre extrêmité (déduit de la géométrie) | N | N | N | N | N | N | N |  |
| `cm_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | N | O | O | O | O | O | O | DP + n° dpt sur 2 digits (ex : DP71) |
| `cm_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | N | O | O | O | O | O | O | Code NRO du département (ex : NRO39086TNI) |
| `cm_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | O | O | O | O | O | O | Code SRO (ex : NRO39086TNI_11) si le cheminement concerne une ZSRO Code Transport (ex : NRO71512SGI_B2) si le cheminement concerne une branche de Transport |
| `cm_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `cm_voie` | `VARCHAR(254)` | Nom ou code (Fantoir par exemple) de la voie où est implanté le cheminement. | N | N | N | N | N | N | N |  |
| `cm_gest_do` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Gestionnaire du domaine emprunté par le cheminement | N | N | N | N | N | N | N |  |
| `cm_prop_do` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Propriétaire du domaine emprunté par le cheminement | N | N | N | N | N | N | N |  |
| `cm_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | N | O | O | O | O | O | O | PRO, EXE ou REC |
| `cm_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat général de l'infrastructure | N | N | N | N | N | N | N |  |
| `cm_datcons` | `DATE` | Date de construction | N | N | N | N | N | N | N |  |
| `cm_datemes` | `DATE` | Date de mise en service | N | N | N | N | N | N | N |  |
| `cm_avct` | `VARCHAR(1)` (`REFERENCES l_avancement(code)`) | Attribut synthétisant l'avancement. Utile pour distinguer en phase d'étude ce qui est existant et à créer. Usage conditionnel. | N | O | O | O | O | O | O |  |
| `cm_typelog` | `VARCHAR(2)` (`REFERENCES l_infra_type_log (code)`) | Type logique de l'infrastructure | N | O | O | O | O | O | O |  |
| `cm_typ_imp` | `VARCHAR(2)` (`REFERENCES l_implantation_type (code)`) | Type d'implantation | N | O | O | O | O | O | O |  |
| `cm_nature` | `VARCHAR(3)` (`REFERENCES l_infra_nature (code)`) | Télécom, eau, gaz, électricité, assainissement, NC | N | N | N | N | N | N | N | Le fermier renseignera TELECOM |
| `cm_compo` | `VARCHAR(254)` | Attribut d'aggrégation décrivant la composition du multitubulaire. Codification Orange conseillée. | N | N | N | N | N | N | N |  |
| `cm_cddispo` | `INTEGER` | Nombre de fourreaux disponibles dans l'artère. Calculable si les relations conduite/cheminement et câble/conduite sont modélisées.   | N | N | N | N | N | N | N |  |
| `cm_fo_util` | `INTEGER` | Attribut d'aggrégation utile si le cablage n'est pas modélisé. Nombre de fibres utiles sur le segment d'infrastructure pour desservir les SUF situés en aval (incluant les besoins de l'infrastructure d'imbrication), corrigé en fonction de la localisation et du dénombrement des Sites Utilisateurs Finaux après relevé terrain. | N | N | N | N | N | N | N |  |
| `cm_mod_pos` | `VARCHAR(20)` (`REFERENCES l_pose_type(code)`) | Technique mise en place pour faire la tranchée. Spécifique aux tranchées. | N | N | N | N | N | N | N | Si GC à créer Déduit du tableau Coupe_Tranchée par rapport à cm_remblai.  Calculé par l'Exploitant |
| `cm_passage` | `VARCHAR(10)` (`REFERENCES l_passage_type(code)`) | Mode de passage. | N | N | N | N | N | N | N | Si GC à créer Déduit du tableau Coupe_Tranchée par rapport à cm_remblai.  Calculé par l'Exploitant |
| `cm_revet` | `VARCHAR(254)` | Type de revêtement de la chaussée. Spécifique aux tranchées. | N | N | N | N | N | N | N | Si GC à créer Déduit du tableau Coupe_Tranchée par rapport à cm_remblai.  Calculé par l'Exploitant |
| `cm_remblai` | `VARCHAR(254)` | Type du remblais. Spécifique aux tranchées. Possibilité de faire référence à un code de coupe de tranchée. | N | N | C | C | C | C | C | Si GC à créer Le champ prend l'une des valeurs listées dans l'onglet "Coupe_Tranchée" |
| `cm_charge` | `NUMERIC(5,2)` | Profondeur en mètres entre la génératrice supérieure des fourreaux et la surface du revêtement. Spécifique aux tranchées. | N | N | N | N | N | N | N | Si GC à créer Déduit du tableau Coupe_Tranchée par rapport à cm_remblai.  Calculé par l'Exploitant |
| `cm_larg` | `NUMERIC(4,2)` | Largeur de la tranchée en mètre. Spécifique aux tranchées. | N | N | N | N | N | N | N | Si GC à créer Déduit du tableau Coupe_Tranchée par rapport à cm_remblai.  Calculé par l'Exploitant |
| `cm_fildtec` | `BOOLEAN` | Présence ou non du fil de détection en fond de fouille dans la tranchée. Spécifique aux tranchées. | N | N | N | N | N | N | N | Si GC à créer Déduit du tableau Coupe_Tranchée par rapport à cm_remblai.  Calculé par l'Exploitant |
| `cm_mut_org` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Nom de l'entité à l'origine des travaux (Opérateurs, FT, Syndicats…) dans le cas d'une construction mutualisée (L49 ou non). Si c'est une co-construction, saisir le leader. | N | N | N | N | N | N | N |  |
| `cm_long` | `NUMERIC(8,2)` | Longueur en mètres (déduite de sa géométrie) | N | O | O | O | O | O | O |  |
| `cm_lgreel` | `NUMERIC(8,2)` | Longueur en mètres mesurée sur le terrain ou estimée. | N | N | N | N | N | N | N |  |
| `cm_comment` | `VARCHAR(254)` | Commentaires | N | N | N | N | N | N | N |  |
| `cm_dtclass` | `VARCHAR(2)` (`REFERENCES l_geoloc_classe(code)`) | Classe de précision au sens du décret DT-DICT | N | N | N | N | ? | ? | ? | Si GC à créer Déduit du tableau Coupe_Tranchée |
| `cm_geolqlt` | `NUMERIC(6,2)` | Précision du positionnement de l'objet, estimée en mètres. La précision doit être déduite du mode d'implantation et du support d'implantation, en tenant compte selon les cas du cumul des imprécisions : des levés ou du fond de plan (utiliser dans ce cas la classe de précision planimétrique au sens de l'arrêté du 16 septembre 2003), de l'outil de détection, des cotations, de l'éventuel report 'à main levée', etc. | N | N | N | N | N | N | N |  |
| `cm_geolmod` | `VARCHAR(4)` (`REFERENCES l_geoloc_mode(code)`) | Mode d'implantation de l'objet. | N | N | N | N | N | N | N |  |
| `cm_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `cm_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `cm_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `cm_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `cm_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `cm_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |
| `geom` | `Geometry(Linestring,2154)` | Ligne | N | O | O | O | O | O | O |  |

---

## `t_cond_chem`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `dm_cd_code` | `VARCHAR(254)` (`REFERENCES t_conduite(cd_code)`) | Code conduite | N | O | O | O | O | O | O |  |
| `dm_cm_code` | `VARCHAR(254)` (`REFERENCES t_cheminement(cm_code)`) | Code de cheminement. | N | O | O | O | O | O | O |  |
| `dm_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `dm_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `dm_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `dm_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `dm_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_conduite`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `cd_code` | `VARCHAR(254)` | Code de la conduite | N | O | O | O | O | O | O |  |
| `cd_codeext` | `Varchar(254)` | Code chez un tiers ou dans une autre base de données. | N | N | N | N | N | N | N |  |
| `cd_etiquet` | `VARCHAR(254)` | Etiquette sur le terrain | N | N | N | N | N | N | N |  |
| `cd_cd_code` | `VARCHAR(254)` | Code du fourreau qui accueille le fourreau si celui-ci est un sous-tube. | N | N | N | N | N | N | N |  |
| `cd_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | N | N | N | N | N | N | N |  |
| `cd_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | N | N | N | N | N | N | N |  |
| `cd_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | N | N | N | N | N | N |  |
| `cd_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `cd_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Propriétaire du fourreau | N | O | O | O | O | O | O | Si GC à créer => Collectivité : Toutes les conduites posées doivent être renseignées.  Si BLO => Orange : Uniquement les conduites utilisées |
| `cd_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Gestionnaire du fourreau | N | C | C | C | C | C | C | Si GC à créer |
| `cd_user` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Utilisateur du fourreau | N | N | N | N | N | N | N |  |
| `cd_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `cd_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | N | O | O | O | O | O | O | PRO, EXE ou REC |
| `cd_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | État | N | N | N | N | N | N | N |  |
| `cd_dateaig` | `DATE` | Date de la dernière opération d'aiguillage. Spécifique aux fourreaux. | N | N | N | N | N | N | N |  |
| `cd_dateman` | `DATE` | Date de la dernière opération de mandrinage. Spécifique aux fourreaux. | N | N | N | N | N | N | N |  |
| `cd_datemes` | `Date` | Date de mise en service | N | N | N | N | N | N | N |  |
| `cd_avct` | `VARCHAR(1)` (`REFERENCES l_avancement(code)`) | Attribut synthétisant l'avancement. Utile pour distinguer en phase d'étude ce qui est existant et à créer. Usage conditionnel. | N | O | O | O | O | O | O | E si la conduite est existante. Y compris GC MED.  C ou T si conduite à créer ou en travaux.  |
| `cd_type` | `VARCHAR(10)` (`REFERENCES l_conduite_type (code)`) | Type de conduite. | N | N | N | N | C | C | C | A renseigner pour le GC crée pour la collectivité |
| `cd_dia_int` | `INTEGER` | Diamètre intérieur du fourreau en mm | N | N | N | N | C | C | C | A renseigner pour le GC crée pour la collectivité |
| `cd_dia_ext` | `INTEGER` | Diamètre extérieur du fourreau en mm | N | N | N | N | N | N | N |  |
| `cd_color` | `VARCHAR(254)` | Couleur du fourreau | N | N | N | N | N | N | N |  |
| `cd_long` | `NUMERIC(8,2)` | Longueur en mètres (calculable depuis cheminement) | N | N | N | N | N | N | N |  |
| `cd_nbcable` | `INTEGER` | Nombre de câbles (attribut calculable) | N | N | N | N | N | N | N |  |
| `cd_occup` | `NUMERIC(3,0)` | Occupation du fourreau en pourcentage | N | N | N | N | N | N | N |  |
| `cd_comment` | `VARCHAR(254)` | Commentaires | N | N | N | N | N | N | N |  |
| `cd_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `cd_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `cd_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `cd_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `cd_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_ebp`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `bp_code` | `VARCHAR(254)` | Code de la BPE, etc. | N | O | O | O | O | O | O |  |
| `bp_etiquet` | `VARCHAR(254)` | Etiquette sur le terrain | N | N | O | O | O | O | O | Pour les boitiers, nommage à respecter sur les étiquettes terrain :  NRO/PT AAXXXX issu du "bp_codext" Le propriétaire ainsi que le type de boitier seront ajoutés ==> voir règles d'étiquetage |
| `bp_codeext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | N | N | O | O | O | O | O | Référence règles de nommage des éléments FTTH remise à chaque bureau d'étude La règle de nommage pour les boîtiers est de la forme suivante : INSEE/NRO/PT AAXXXX (Attention, il ne doit pas y avoir d’espace avant et après les / et il doit y avoir un espace après PT) avec : - INSEE => 5 caractères  (code INSEE de la commune d’implantation du NRO) - NRO => 3 caractères du trigramme du NRO interne de l’Exploitant qui est différent de la référence du projet (fichier annexe 1)  - AA  => 2 digits d’identification du créateur  - XXXX :  o démarrage à 0001 pour le premier élément rencontré de l’étude o incrément ensuite jusqu’à 9999 o Soit la possibilité d’avoir 19 998 n° de boîtier par NRO  |
| `bp_pt_code` | `VARCHAR(254)` (`REFERENCES t_ptech(pt_code)`) | Code point technique | N | C | C | C | C | C | C | Obligatoire sauf pour PB en immeuble |
| `bp_lt_code` | `VARCHAR(254)` (`REFERENCES t_ltech(lt_code)`) | Code de local technique, pour le cas où un élément de branchement passif serait présent dans un site technique et non dans ou sur un point technique. | N | C | C | C | C | C | C | Obligatoire si PB en immeuble |
| `bp_sf_code` | `VARCHAR(254)` (`REFERENCES t_suf(sf_code)`) | Identifiant unique du SUF dans lequel est installée la PTO. Cas d'une PTO uniquement | N | C | C | N | N | C | C | Obligatoire si PB en immeuble |
| `bp_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Propriétaire de l'élément | N | N | N | N | N | N | N |  |
| `bp_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Gestionnaire de l'élément | N | N | N | N | N | N | N |  |
| `bp_user` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Utilisateur de l'élément | N | N | N | N | N | N | N |  |
| `bp_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `bp_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | N | O | O | O | O | O | O |  |
| `bp_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | État | N | N | N | N | N | N | N |  |
| `bp_occp` | `VARCHAR(10)` (`REFERENCES l_occupation_type (code)`) | Occupation. | N | N | N | N | N | N | N |  |
| `bp_datemes` | `Date` | Date de mise en service | N | N | N | N | N | N | N |  |
| `bp_avct` | `VARCHAR(1)` (`REFERENCES l_avancement(code)`) | Attribut synthétisant l'avancement. Utile pour distinguer en phase d'étude ce qui est existant et à créer. Usage conditionnel. | N | N | N | N | O | O | O | NOK à l'EXE Au REC, mettre "EXISTANT" pour déclencher la MAD PM (PEP) et la MAD Site (PBO).   |
| `bp_typephy` | `VARCHAR(5)` (`REFERENCES l_bp_type_phy (code)`) | Type physique d'élément de branchement passif. Capacité de soudure. | N | O | O | O | O | O | O |  |
| `bp_typelog` | `VARCHAR(3)` (`REFERENCES l_bp_type_log (code)`) | Type de l'élément | N | O | O | O | O | O | O |  |
| `bp_rf_code` | `VARCHAR(254)` (`REFERENCES t_reference (rf_code)`) | Référence. | N | N | O | O | O | O | O |  |
| `bp_entrees` | `INTEGER` | Nombre d'entrées de câbles. | N | N | N | N | N | N | N |  |
| `bp_ref_kit` | `VARCHAR(30)` | Référence du kit d'entrée de câble utilisé | N | N | N | N | N | N | N |  |
| `bp_ca_nb` | `INTEGER` | Nombre de cassettes contenues dans le BPE | N | N | N | N | O | O | O | NOK à l'EXE |
| `bp_nb_pas` | `INTEGER` | Nombre de pas de l'organiseur du BPE | N | N | N | N | N | N | N |  |
| `bp_linecod` | `VARCHAR(12)` | Code d'une ligne (cas FTTH) selon la nomenclature du régulateur. Cas d'un PTO. (OO-XXXX-XXXX) | N | N | N | N | N | N | N |  |
| `bp_oc_code` | `VARCHAR(50)` | Référence OC (Opérateur Commercial) de la prise terminale. Différent de bp_code. Cas d'une PTO uniquement | N | N | N | N | N | N | N |  |
| `bp_racco` | `VARCHAR(6)` (`REFERENCES l_bp_racco(code)`) | Codification Interop de l'échec du raccordement. Cas d'une PTO uniquement. | N | N | N | N | N | N | N |  |
| `bp_comment` | `VARCHAR(254)` | commentaires | N | N | N | N | N | N | N |  |
| `bp_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `bp_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `bp_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `bp_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `bp_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_fibre`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `fo_code` | `VARCHAR(254)` | Identifiant unique de la fibre | N | N | O | O | O | O | O |  |
| `fo_code_ext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | N | N | N | N | N | N | N |  |
| `fo_cb_code` | `VARCHAR(254)` (`REFERENCES t_cable (cb_code)`) | Identifiant unique du câble auquel la fibre appartient | N | N | O | O | O | O | O |  |
| `fo_nincab` | `INTEGER` | Numéro de fibre dans le câble | N | N | O | O | O | O | O | Dans le respect de la numérotation définie dans le document de mise en œuvre Grace THD.  |
| `fo_numtub` | `INTEGER` | Numéro du tube auquel appartient la fibre | N | N | C | C | C | C | C | Dans le respect de la numérotation définie dans le document de mise en œuvre Grace THD.  |
| `fo_nintub` | `INTEGER` | Numéro de la fibre dans le tube (1 à 12, …) | N | N | C | C | C | C | C | Dans le respect de la numérotation définie dans le document de mise en œuvre Grace THD.  |
| `fo_type` | `VARCHAR(20)` (`REFERENCES l_fo_type (code)`) | Type de fibre (G652, G655, G657, etc.) | N | N | O | O | O | O | O |  |
| `fo_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat de fonctionnement de la fibre. | N | N | N | N | N | N | N |  |
| `fo_color` | `VARCHAR(10)` (`REFERENCES l_fo_color(code)`) | Numéro de fibre selon le code couleur (valeurs à adapter aux usages). Possibilité d'utiliser une combinaison c.n où c serait un codage et n le numéro de fibre dans ce codage. (ex : 1.1 pourrait être le rouge dans le codage FT). | N | N | C | C | C | C | C | Dans le respect de la numérotation définie dans le document de mise en œuvre Grace THD.  |
| `fo_reper` | `VARCHAR(5)` (`REFERENCES l_tube (code)`) | Repérage du tube | N | N | N | N | N | N | N |  |
| `fo_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `fo_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `fo_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | O | O | O | O | O |  |
| `fo_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `fo_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `fo_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `fo_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_love`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `lv_id` | `BIGINT` | Identifiant unique pouvant être auto-incrémenté (selon plages d'identitifiants) | N | N | N | N | O | O | O |  |
| `lv_cb_code` | `VARCHAR(254)` (`REFERENCES t_cable (cb_code)`) | Code du câble | N | N | N | N | O | O | O | Pour chaque point technique contenant un boitier, une ligne de t_love pour indiquer 30 mètres (15 mètres de chaque côté).  NOK à l'EXE |
| `lv_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code du nœud dans lequel est positionné ce love | N | N | N | N | O | O | O | Pour chaque point technique contenant un boitier, une ligne de t_love pour indiquer 30 mètres (15 mètres de chaque côté).  NOK à l'EXE |
| `lv_long` | `INTEGER` | longueur du love du câble dans le nœud en mètre | N | N | N | N | O | O | O |  |
| `lv_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | N | N | O | O | O |  |
| `lv_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `lv_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `lv_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `lv_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_ltech`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `lt_code` | `VARCHAR(254)` | Code local technique | O | O | O | O | O | O | O |  |
| `lt_codeext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | O | N | O | O | O | O | O |  Pour le SRO =>         INSEE/NRO/PT AAXXXX (NRO = trigramme de l'Exploitant qui est différent de la référence projet de la collectivité)   avec : - INSEE => 5 caractères  (code INSEE de la commune d’implantation du NRO) - NRO => 3 caractères du trigramme du NRO interne de l’Exploitant qui est différent de la référence du projet  - AA  => 2 digits d’identification du créateur  - XXXX => 4 digits d'incrémentation (unique à la ZA NRO) |
| `lt_etiquet` | `VARCHAR(20)` | Nom du local technique tel qu'étiqueté sur le terrain (selon règles et plages de nommage) | N | N | N | N | N | N | N | géré par exploitant  |
| `lt_st_code` | `VARCHAR(254)` (`REFERENCES t_sitetech (st_code)`) | Identifiant unique contenu dans la table SITE_TECHNIQUE | O | O | O | O | O | O | O |  |
| `lt_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du propriétaire du local technique. | N | N | N | N | N | N | N |  |
| `lt_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant unique du gestionnaire. | N | N | N | N | N | N | N |  |
| `lt_user` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant de l'utilisateur | N | N | N | N | N | N | N |  |
| `lt_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `lt_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Identifiant unique du statut de déploiement. | O | O | O | O | O | O | O | PRO, EXE ou REC |
| `lt_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat du local. | N | N | N | N | N | N | N |  |
| `lt_dateins` | `DATE` | Date d'installation | N | N | N | N | N | N | N |  |
| `lt_datemes` | `DATE` | Date de mise en service du local technique | N | N | N | N | N | N | N |  |
| `lt_local` | `VARCHAR (254)` | Informations de localisation | N | N | N | N | N | N | N |  |
| `lt_elec` | `BOOLEAN` | Présence d'une alimentation électrique | N | N | N | N | N | N | N |  |
| `lt_clim` | `VARCHAR(6)` (`REFERENCES l_clim_type (code)`) | Présence et type du système éventuel de ventilation ou de climatisation. | N | N | N | N | N | N | N |  |
| `lt_occp` | `VARCHAR(10)` (`REFERENCES l_occupation_type (code)`) | Occupation. | N | N | N | N | N | N | N |  |
| `lt_idmajic` | `VARCHAR(254)` | Identifiant du local dans un référentiel comme la base MAJICIII lorsque disponible. | N | N | N | N | N | N | N |  |
| `lt_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `lt_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | O | O | O | O |  |
| `lt_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `lt_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `lt_abddate` | `DATE` | Date d'abandon de l'objet | C | N | N | N | C | C | C |  |
| `lt_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_ltech_patch201`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `lt_code` | `VARCHAR(254)` (`REFERENCES t_ltech(lt_code)`) | Code local technique | N | N | O | N | N | O | O |  |
| `lt_bat` | `VARCHAR(100)` | Le cas échéant, nom du bâtiment (NULL si adresse =bâtiment) | N | N | N | N | N | N | N |  |
| `lt_escal` | `VARCHAR(20)` | Le cas échéant, nom ou numéro d’escalier du local technique (NULL si adresse = entrée/escalier) | N | N | C | C | C | C | C | Format de données : libre Taille max  : 64 caractères  exemples :   . escalier central  . esc A  . escalier gauche  . 1A  . principal  . Droite  Si renseigné, doit alors correspondre à la valeur sf_escal de suf situés à la même adresse de l’équipement et dans la même montée ( même escalier) |
| `lt_etage` | `VARCHAR(20)` | Le cas échéant, numéro d’étage du local technique. | N | N | C | C | C | C | C | Format de données : format nombre attendu exclusivement. Exemple : -2 (pour Niveau -2) -1 (pour niveau -1) 0 (pour RDC) 1(pour le 1er etage)… et pour les positionnements entre deux paliers 2,5 (equipement entre le 2eme et le 3eme étage) …  |

---

## `t_masque`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `mq_id` | `BIGINT` | Identifiant unique pouvant être auto-incrémenté (selon plages d'identitifiants) | N | N | N | N | C | C | C | À saisir pour toutes les chambres neuves.  |
| `mq_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code de la chambre à laquelle appartient le masque | N | N | N | N | C | C | C | À saisir pour toutes les chambres neuves.  |
| `mq_face` | `VARCHAR(1)` (`REFERENCES l_masque_face (code)`) | Face de la chambre (A, B, C, D, …) | N | N | N | N | C | C | C | À saisir pour toutes les chambres neuves.  |
| `mq_col` | `INTEGER` | Numéro de colonne de l'alvéole concernée | N | N | N | N | C | C | C | À saisir pour toutes les chambres neuves.  |
| `mq_ligne` | `INTEGER` | Numéro de ligne de l'alvéole concernée | N | N | N | N | C | C | C | À saisir pour toutes les chambres neuves.  |
| `mq_cd_code` | `VARCHAR(254)` (`REFERENCES t_conduite (cd_code)`) | Code de la conduite attachée à l'alvéole du masque. | N | N | N | N | C | C | C | À saisir pour toutes les chambres neuves.  |
| `mq_qualinf` | `VARCHAR(3)` (`REFERENCES l_qualite_info(code)`) | Qualité de l'information | N | N | N | N | N | N | N |  |
| `mq_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `mq_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | À saisir pour toutes les chambres neuves.  |
| `mq_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | N | N | N |  |
| `mq_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `mq_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | N | N | N |  |
| `mq_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_noeud`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `nd_code` | `VARCHAR(254)` | Code noeud | O | O | O | O | O | O | O |  |
| `nd_codeext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | N | N | N | N | N | N | N |  |
| `nd_nom` | `VARCHAR(254)` | Nom du nœud (reprendre celui dans la base l'opérateur si il existe) | N | N | N | N | N | N | N |  |
| `nd_coderat` | `VARCHAR(254)` | Code du noeud de rattachement (NRO, PM, …). Valable pour les réseaux hiérarchiques (principalement pour le FTTH). | N | N | N | N | N | N | N |  |
| `nd_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | N | O | O | O | O | O | O | DP + n° dpt sur 2 digits (ex : DP71) |
| `nd_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | N | O | O | O | O | O | O | Code NRO du département (ex : NRO39086TNI) |
| `nd_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | O | O | O | O | O | O | Code SRO du département (ex : NRO39086TNI_11) si le nœud concerne un ZSRO Code Transport (ex: NRO71512SGI_B2) si le nœud concerne une branche de Transport |
| `nd_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `nd_voie` | `VARCHAR(254)` | Adresse de la voie dans laquelle est implanté le nœud (notion utilisée pour la dénomination du nœud et non pour sa géolocalisation). Utilisable lorsqu'un noeud ne peut être positionné à une adresse précise. | C | C | C | C | C | C | C | - A minima : INSEE/Commune - Pour les sites techniques et les points techniques supportant un boitier : INSEE/Commune/Voie ou lieu-dit (à défaut, adresse du SUF le plus proche) - Au mieux : INSEE/Commune/Voie/Numéro/Complément |
| `nd_type` | `VARCHAR(2)` (`REFERENCES l_noeud_type (code)`) | Type du nœud (se déduit de la relation d'héritage) | O | O | O | O | O | O | O |  |
| `nd_type_ep` | `VARCHAR (3)` (`REFERENCES l_technologie_type (code)`) | Liste des technologies présentes (1 à 5 occurrences) | N | N | N | N | N | N | N |  |
| `nd_comment` | `VARCHAR(254)` | Commentaires | N | N | N | N | N | N | N |  |
| `nd_dtclass` | `VARCHAR(2)` (`REFERENCES l_geoloc_classe(code)`) | Classe de précision au sens du décret DT-DICT | N | N | N | N | O | O | O | En EXE : classe C. En REC : classe A.  |
| `nd_geolqlt` | `NUMERIC(6,2)` | Précision du positionnement de l'objet, estimée en mètres. La précision doit être déduite du mode d'implantation et du support d'implantation, en tenant compte selon les cas du cumul des imprécisions : des levés ou du fond de plan (utiliser dans ce cas la classe de précision planimétrique au sens de l'arrêté du 16 septembre 2003), de l'outil de détection, des cotations, de l'éventuel report 'à main levée', etc. | N | N | N | N | N | N | N |  |
| `nd_geolmod` | `VARCHAR(4)` (`REFERENCES l_geoloc_mode(code)`) | Mode d'implantation de l'objet. | N | N | N | N | O | O | O | Sera toujours LEVE APRES LA POSE NOK à l'EXE |
| `nd_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `nd_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | O | O | O | O |  |
| `nd_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `nd_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `nd_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `nd_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |
| `geom` | `Geometry(Point,2154)` | Point abstrait | O | O | O | O | O | O | O |  |

---

## `t_organisme`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `or_code` | `VARCHAR (20)` | Code de l'organisme | O | O | O | O | O | O | O |  |
| `or_siren` | `VARCHAR(9)` | numéro SIREN de l'opérateur, de la collectivité, … | N | N | N | N | N | N | N |  |
| `or_nom` | `VARCHAR(254)` | Nom de l'opérateur, de la collectivité, de l'entreprise, etc. | O | O | O | O | O | O | O |  |
| `or_type` | `VARCHAR(254)` | Classification juridique. Littéral ou nomenclature INSEE. | N | O | O | O | O | O | O |  |
| `or_activ` | `VARCHAR(254)` | Activité principale exercée. Littéral ou Code NAF. | N | N | N | N | N | N | N |  |
| `or_l331` | `VARCHAR(254)` | Code court selon liste opérateurs L33-1 téléchargeable sur le site de l'ARCEP | N | N | N | N | N | N | N |  |
| `or_siret` | `VARCHAR(14)` | numéro SIRET dans le cas d'un établissement (sens INSEE, base SIRENE) | C | C | C | C | C | C | C | A renseigner pour les syndics en personne morale,  Le siret doit obligatoirement être renseigné dans la même livraison de données que le renseignement de l'organisme dans l'attribut ad_gest,  Si le Siret n'est pas renseigné le cas est traduit comme syndic "unipropriétaire".   |
| `or_nometab` | `VARCHAR(254)` | Nom de l'établissement, de l'agence (sens INSEE, base SIRENE) | N | N | N | N | N | N | N |  |
| `or_ad_code` | `VARCHAR(254)` (`REFERENCES t_adresse(ad_code)`) | Identifiant de l'adresse dans la table t_adresse. Seulement s'il s'agit d'une adresse référencée dans la table adresse. | N | N | N | N | N | N | N |  |
| `or_nomvoie` | `VARCHAR (254)` | Nom de la voie | C | C | C | C | C | C | C |  |
| `or_numero` | `INTEGER` | Numéro  éventuel  de  l’adresse  dans  la voie | C | C | C | C | C | C | C |  |
| `or_rep` | `VARCHAR (20)` | Indice de répétition associé au numéro (par exemple Bis, A, 1…) | C | C | C | C | C | C | C |  |
| `or_local` | `VARCHAR(254)` | Complément d'adresse pour identifier le local. | C | C | C | C | C | C | C |  |
| `or_postal` | `VARCHAR(20)` | Code  postal  du  bureau  de  distribution de la voie | C | C | C | C | C | C | C |  |
| `or_commune` | `VARCHAR (254)` | Nom officiel de la commune | C | C | C | C | C | C | C |  |
| `or_telfixe` | `VARCHAR(20)` | Téléphone fixe | C | C | C | C | C | C | C |  |
| `or_mail` | `VARCHAR(254)` | Mail de contact générique | C | C | C | C | C | C | C |  |
| `or_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `or_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | O | O | O | O |  |
| `or_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | C | C | C | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `or_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `or_abddate` | `DATE` | Date d'abandon de l'objet | C | C | C | C | C | C | C |  |
| `or_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_position`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `ps_code` | `VARCHAR(254)` | Code unique. | N | N | O | O | O | O | O |  |
| `ps_numero` | `INTEGER` | Position (numéro de compartiment) du smoove ou du connecteur | N | N | O | O | O | O | O | Numéro de fibre dans une cassette de boite ou une cassette de tiroir.   Cf. onglet "MODELISATION POSITION"  ps_numero = [ (fo_nincab -1 ) modulo (capacité de la cassette) ] +1. Il s’agit donc d’un nombre entier compris entre 1 et la capacité de la cassette. |
| `ps_1` | `VARCHAR (254)` (`REFERENCES t_fibre (fo_code)`) | Code unique d'une fibre de la table FIBRE. (pour continuité route optique) | N | N | C | C | C | C | C | Seules les positions contenant au moins une fibre sont décrites.  Les positions sont orientées dans le sens NRO vers PBO :  Transport : Côté NRO Distri : Côté SRO |
| `ps_2` | `VARCHAR (254)` (`REFERENCES t_fibre (fo_code)`) | Code unique d'une fibre de la table FIBRE. (pour continuité route optique) | N | N | C | C | C | C | C | Seules les positions contenant au moins une fibre sont décrites.  Les positions sont orientées dans le sens NRO vers PBO :  Transport : Côté SRO Distri : Côté PB |
| `ps_cs_code` | `VARCHAR(254)` (`REFERENCES t_cassette (cs_code)`) | Identifiant unique de la CASSETTE à laquelle appartient la position. (le cas échéant) | N | N | C | C | C | C | C | Obligatoire si la cassette contient au moins une fibre, donc une position (ps_1 ou ps_2 rempli).  |
| `ps_ti_code` | `VARCHAR(254)` (`REFERENCES t_tiroir (ti_code)`) | Identifiant unique du TIROIR / de la TCOP à laquelle appartient la position. (cas échéant) | N | N | C | C | C | C | C | Obligatoire si cassette contenue dans un tiroir |
| `ps_type` | `VARCHAR(10)` (`REFERENCES l_position_type (code)`) | Type de connecteur / soudure. | N | N | N | N | N | N | N |  |
| `ps_fonct` | `VARCHAR(2)` (`REFERENCES l_position_fonction (code)`) | Type de connectorisation (Connecteur, epissure, pigtail, ….) | N | N | O | O | O | O | O | 1. Câble en passage dans une cassette : PA – PASSAGE  2. Câble qui arrive sur un connecteur de tiroir ou tête de câble : CO – CONNECTEUR  3. Câble avec soudure dans cassette : EP – EPISSURE  4. Fibre en attente : AT – ATTENTE  |
| `ps_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat de fonctionnement de la position / du corps de traversée, | N | N | N | N | N | N | N |  |
| `ps_preaff` | `VARCHAR(50)` | Pré-affectation de la route optique au SUF de l'IP, ou de l'IPE ou à l'Infrastructure d'Imbrication. | N | N | C | C | C | C | C | Pour les fibres non disponibles, renseigner "RESERVE_COLLECTIVITE" |
| `ps_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `ps_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | O | O | O | O | O |  |
| `ps_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `ps_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `ps_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `ps_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_ptech`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `pt_code` | `VARCHAR(254)` | Code du point technique | N | O | O | O | O | O | O |  |
| `pt_codeext` | `Varchar(254)` | Code chez un tiers ou dans une autre base de données. | N | N | O | O | O | O | O | Référence règles de nommage des éléments FTTH remise à chaque bureau d'étude La règle de nommage pour les appuis à créer appartenant à la collectivité est de la forme suivante : ABBAAXX/INSEE (Attention, il ne doit pas y avoir d’espace avant et après le /) avec : - BB => n° de département (21, 39, 58 ou 71)          -  AA => 2 digits d’identification du créateur - XX => 2 digits d’incrémentation o démarrage à 01 pour le premier élément rencontré de l’étude o incrément ensuite jusqu’à 99 - INSEE => code INSEE de la commune où est installé l’appui (à différencier du n° INSEE de la commune du NRO)  La règle de nommage pour les chambres à créer appartenant à la collectivité est de la forme suivante : CBBAAXX/INSEE (Attention, il ne doit pas y avoir d’espace avant et après le /) avec : - BB => n° de département (21, 39, 58 ou 71)          -  AA => 2 digits d’identification du créateur - XX : => 2 digits d’incrémentation o démarrage à 01 pour le premier élément rencontré de l’étude o incrément ensuite jusqu’à 99 o soit la possibilité d’avoir 198 n° de chambre - INSEE => code INSEE de la commune où est installée la chambre (à différencier du n° INSEE de la commune du NRO)  La règle de nommage pour les appuis et chambres existants est de reprendre le nommage du propriétaire  Pour les chambres Orange ou chambres Tiers présentes dans le PIT: code ch1/code ch2 (avec code ch1 sur 5 caractères donc précédé de 0 si nécessaire)  Pour les appuis Orange :    num_appui/code_commune (avec numéro d'appui sur 7 caractères donc précédé de 0 si nécessaire)    Pour les cas où il n’y a pas de nommage, utiliser le nommage ci-dessous : Pour les appuis :   AAAXXX/INSEE Pour les chambres : CAAXXX/INSEE avec : - AA => 2 digits d’identification du créateur - XXX : => 3 digits d’incrémentation o démarrage à 01 pour le premier élément rencontré de l’étude o incrément ensuite jusqu’à 999 o soit la possibilité d’avoir 1 998 n° de chambre - INSEE => code INSEE de la commune où est installée la chambre (à différencier du n° INSEE de la commune du NRO)  |
| `pt_etiquet` | `VARCHAR(254)` | Etiquette sur le terrain | N | N | O | O | O | O | O | Pour les appuis et les chambres créés appartenants à la collectivité, nommage à respecter sur les étiquettes terrain :  "pt_codext" Seront ajoutés le nom du propriétaire ainsi que la date de pose  ==> voir règles d'étiquetage |
| `pt_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code noeud | N | O | O | O | O | O | O |  |
| `pt_ad_code` | `VARCHAR(254)` (`REFERENCES t_adresse(ad_code)`) | Identifiant unique contenu dans la table t_adresse. Si le point technique n'est pas localisé à une adresse postale précise, nd_voie permet une localisation à l'adresse moins précise. | N | N | N | N | N | N | N |  |
| `pt_gest_do` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Gestionnaire de la voirie | N | N | N | N | N | N | N |  |
| `pt_prop_do` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Propriétaire de la voirie | N | N | N | N | N | N | N |  |
| `pt_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Propriétaire | N | O | O | O | O | O | O |  |
| `pt_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Gestionnaire | N | O | O | O | O | O | O |  |
| `pt_user` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Utilisateur | N | N | N | N | N | N | N |  |
| `pt_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `pt_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | N | O | O | O | O | O | O |  |
| `pt_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | État du point technique | N | N | N | N | N | N | N |  |
| `pt_dateins` | `DATE` | Date d'installation | N | N | N | N | C | C | C | NOK à l'EXE |
| `pt_datemes` | `Date` | Date de mise en service | N | N | N | N | N | N | N |  |
| `pt_avct` | `VARCHAR(1)` (`REFERENCES l_avancement(code)`) | Attribut synthétisant l'avancement. Utile pour distinguer en phase d'étude ce qui est existant et à créer. Usage conditionnel. | N | O | O | O | O | O | O | NOK à l'EXE EXISTANT si pt installé et fonctionnel.  Sinon, A CRÉER ou TRAVAUX.  Bien de retour suite MAD : EN SERVICE.  |
| `pt_typephy` | `VARCHAR(1)` (`REFERENCES l_ptech_type_phy (code)`) | Type de point technique | N | O | O | O | O | O | O |  |
| `pt_typelog` | `VARCHAR(1)` (`REFERENCES l_ptech_type_log (code)`) | Usage du point technique | N | N | N | N | N | N | N |  |
| `pt_rf_code` | `VARCHAR(254)` (`REFERENCES t_reference (rf_code)`) | Référence. | N | N | N | N | N | N | N |  |
| `pt_nature` | `VARCHAR (20)` (`REFERENCES l_ptech_nature (code)`) | Nature du point technique. | N | C | C | C | C | C | C | Obligatoire pour les points techniques appartenant à la collectivité et a créer La valeur la plus proche sera utilisée pour une valeur absente de la liste.  |
| `pt_secu` | `BOOLEAN` | Point technique équipé d'un système de verrouillage, ou tout autre système permettant d'en sécuriser l'accès. | N | O | O | O | O | O | O |  |
| `pt_occp` | `VARCHAR(10)` (`REFERENCES l_occupation_type (code)`) | Occupation. | N | N | N | N | N | N | N |  |
| `pt_a_dan` | `NUMERIC` | Effort disponible après pose (exprimé en daN – décanewtons) | N | N | N | N | N | N | N |  |
| `pt_a_dtetu` | `DATE` | Date de l'étude de charge | N | N | N | N | N | N | N |  |
| `pt_a_struc` | `VARCHAR(100)` | Simple, Moisé, Haubané, Couple, … | N | N | C | C | C | C | C | Obligatoire pour les poteaux créés |
| `pt_a_haut` | `NUMERIC(5,2)` | Hauteur en mètre entre le sol et la base de l'infrastructure (réseau en façade ou aérien) | N | N | C | C | C | C | C | Obligatoire pour les poteaux créés |
| `pt_a_passa` | `BOOLEAN` | 0 si uniquement pour passage de câbles | N | N | N | N | N | N | N |  |
| `pt_a_strat` | `BOOLEAN` | Notion Orange disponible dans les PIT. Notion potentiellement extensible à d'autres types de réseaux. | N | N | N | N | N | N | N |  |
| `pt_rotatio` | `NUMERIC(5,2)` | Angle du grand axe du point technique en degrés dans le sens retrograde (sens des aiguilles d'une montre) à partir du Nord. | N | N | N | N | N | N | N |  |
| `pt_detec` | `BOOLEAN` | Présence d'un boitier pour un fil de détection. | N | N | N | N | C | C | O | NOK à l'EXE Obligatoire si chambre |
| `pt_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `pt_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `pt_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `pt_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `pt_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `pt_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_reference`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `rf_code` | `VARCHAR(254)` | Code permettant d'identifier la référence d'un matériel dans la base. | N | O | O | O | O | O | O | Obligatoire pour les matériel de type Baie, Tiroir, Cassette, EBP, Câbles  |
| `rf_type` | `VARCHAR(2)` (`REFERENCES l_reference_type (code)`) | Type de matériel | N | O | O | O | O | O | O |  |
| `rf_fabric` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Fabricant | N | O | O | O | O | O | O |  |
| `rf_design` | `VARCHAR(254)` | Design | N | O | O | O | O | O | O |  |
| `rf_etat` | `VARCHAR(1)` (`REFERENCES l_reference_etat (code)`) | Disponibilité de la référence | N | N | N | N | N | N | N |  |
| `rf_comment` | `VARCHAR(254)` | Commentaires | N | C | C | C | C | C | C |  |
| `rf_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | O | O | O | O |  |
| `rf_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | C | C | C | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `rf_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `rf_abddate` | `DATE` | Date d'abandon de l'objet | N | C | C | C | C | C | C |  |
| `rf_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_ropt`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `rt_id` | `BIGINT` | Identifiant unique pouvant être auto-incrémenté | N | N | N | N | N | N | N |  |
| `rt_code` | `VARCHAR(254)` | Code de la route optique. Se conformer aux règles de nommage. Ce code n'est pas unique puisqu'il est à répéter autant de fois qu'il y a de fibres constituant la route optique. | N | N | N | N | N | N | N |  |
| `rt_code_ext` | `VARCHAR(254)` | Nom de la route optique dans un système d'information externe. | N | N | N | N | N | N | N |  |
| `rt_fo_code` | `VARCHAR(254)` (`REFERENCES t_fibre (fo_code)`) | Code de la fibre. | N | N | N | N | N | N | N |  |
| `rt_fo_ordr` | `INTEGER` | Numéro d'ordre de la fibre pour la composition de la route optique. Cette valeur peut être calculée, le renseignement de cet attribut est à réserver à des usages spécifiques. | N | N | N | N | N | N | N |  |
| `rt_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `rt_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | N | N | N | N | N |  |
| `rt_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | N | N | N | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `rt_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `rt_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | N | N | N |  |
| `rt_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_sitetech`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `st_code` | `VARCHAR(254)` | Code du site | O | O | O | O | O | O | O |  |
| `st_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Identifiant unique contenu dans la table Noeud | O | O | O | O | O | O | O |  |
| `st_codeext` | `VARCHAR (254)` | Code chez un tiers ou dans une autre base de données. | O | N | N | N | N | N | N |  |
| `st_nom` | `VARCHAR (254)` | Nom du site. | N | C | C | C | C | C | C | Renseigner le nom des sites techniques SRO et NRO (Pas les sites complexes type immeuble) Exemple de valeurs : NRO71543TOI_11_S NRO71543TOI_00_S |
| `st_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du propriétaire du site. | O | O | O | O | O | O | O |  |
| `st_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du gestionnaire du site. | O | O | O | O | O | O | O |  |
| `st_user` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | utilisateur du site | N | N | N | N | N | N | N |  |
| `st_proptyp` | `VARCHAR(3)` (`REFERENCES l_propriete_type (code)`) | Type de propriété | N | N | N | N | N | N | N |  |
| `st_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | O | O | O | O | O | O | O | PRO, EXE ou REC |
| `st_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat du site. | N | N | N | N | N | N | N |  |
| `st_dateins` | `DATE` | Date d'installation | N | N | N | N | N | N | N |  |
| `st_datemes` | `DATE` | Date de mise en service | N | N | N | N | N | N | N |  |
| `st_avct` | `VARCHAR(1)` (`REFERENCES l_avancement(code)`) | Attribut synthétisant l'avancement. Utile pour distinguer en phase d'étude ce qui est existant et à créer. Usage conditionnel. | O | O | O | O | O | O | O | - Consultation de lot : C - A CREER - REC Transport : E - EXISTANT - REC Distri : S - EN SERVICE |
| `st_typephy` | `VARCHAR(3)` (`REFERENCES l_site_type_phy (code)`) | Type physique du site (shelter, armoire de rue, bâti). | O | O | O | O | O | O | O | NRA ou site client = BAT, Shelter = SHE et armoire de rue = ADR.  |
| `st_typelog` | `VARCHAR(10)` (`REFERENCES l_site_type_log (code)`) | Type logique du site | O | O | O | O | O | O | O | Renseigner "NRO" si NRO, "SRO" si SRO |
| `st_nblines` | `INTEGER` | Nombre de lignes du site. | N | N | N | N | N | N | N |  |
| `st_ad_code` | `VARCHAR(254)` (`REFERENCES t_adresse (ad_code)`) | Identifiant unique contenu dans la table ADRESSE | N | N | N | N | N | N | N |  |
| `st_comment` | `VARCHAR(254)` | Commentaire | N | N | O | N | N | O | O | Permet d'identifier la couleur des armoires.  Concaténation [code couleur]  [type de revêtement] . Exemple : RAL 7035 standard |
| `st_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | O | O | O | O |  |
| `st_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `st_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `st_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `st_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_suf`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `sf_code` | `VARCHAR(254)` | Code du SUF. | N | O | O | N | N | O | O |  |
| `sf_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code du nœud auquel se rattache le SUF. Un nœud peut être partagé avec un site. | N | O | O | N | N | O | O |  |
| `sf_ad_code` | `VARCHAR(254)` (`REFERENCES t_adresse (ad_code)`) | Identifiant unique de la table ADRESSE (adresse postale du bâti) | N | O | O | N | N | O | O |  |
| `sf_zp_code` | `VARCHAR(254)` (`REFERENCES t_zpbo (zp_code)`) | Identifiant unique de la zone arrière de PBO couvrant le SUF. | N | O | O | N | N | O | O |  |
| `sf_escal` | `VARCHAR (20)` | Escalier, pour les habitats collectifs. | N | N | C | N | N | C | C | Si SUF inclut dans un immeuble |
| `sf_etage` | `VARCHAR (20)` | Etage, pour les habitats collectifs. | N | N | C | N | N | C | C | Si SUF inclut dans un immeuble |
| `sf_oper` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant de l'opérateur d'immeuble dans la table organisme. | N | N | N | N | N | N | N |  |
| `sf_type` | `VARCHAR(1)` (`REFERENCES l_suf_type (code)`) | Type de Site Utilisateur Final. | N | O | O | N | N | O | O |  |
| `sf_prop` | `VARCHAR(254)` | Code permettant d'identifier le propriétaire dans la base de données interne. Les informations personnelles sont traitées en dehors du standard d'échange. | N | N | N | N | N | N | N |  |
| `sf_resid` | `VARCHAR(254)` | Code permettant d'identifier le résidant dans la base de données interne. Les informations personnelles sont traitées en dehors du standard d'échange. | N | N | N | N | N | N | N |  |
| `sf_local` | `VARCHAR (254)` | Informations de localisation du Site Utilisateur Final. Champ libre. | N | N | N | N | N | N | N |  |
| `sf_racco` | `VARCHAR(2)` (`REFERENCES l_suf_racco(code)`) | Etat du raccordement selon la terminologie du régulateur. | N | O | O | N | N | O | O | PR au PRO |
| `sf_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `sf_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | N | N | O | O |  |
| `sf_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | N | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `sf_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `sf_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | N | C | C |  |
| `sf_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_tiroir`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `ti_code` | `VARCHAR(254)` | Code du tiroir optique | N | N | O | O | O | O | O |  |
| `ti_codeext` | `VARCHAR(254)` | Code chez un tiers ou dans une autre base de données. | N | N | O | O | O | O | O | Référence règles de nommage des éléments FTTH remise à chaque bureau d'étude Doit contenir le n° du tiroir dans la baie. Numérotation en partant du haut vers le bas  Armoire 2x28U (maxi 8 tiroirs)  • Partie droite de l’armoire : o Tiroir 1 = plateau de stockage o Tiroirs 2 à 6 = ITOM144 o Tiroir 8 = ITOM 48 • Partie gauche de l’armoire : o Tiroirs ITOM48 : 1er tiroir en bas =  tiroir 8 puis décrémenter si d’autres ITOM 48 sont installés  Armoire 2x40U (maxi 13 tiroirs) • Partie droite de l’armoire : o Tiroir 1 = plateau de stockage o Tiroirs 2 à 8 = ITOM144 o Tiroir 13 = ITOM48  • Partie gauche de l’armoire : Tiroirs ITOM48 = 1er tiroir en bas =  tiroir 13 puis décrémenter si d’autres ITOM 48 sont installés  Si le ti-codeext a la valeur 0 on considère que c'est un tiroir de réserve installé mais non utilisé.  |
| `ti_etiquet` | `VARCHAR(254)` | Etiquette sur le terrain | N | N | O | O | O | O | O | Pour les tiroirs, nommage à respecter sur les étiquettes terrain :  Pour les NRO :  "NRO/TR XX XXXX" issu du cb_codeext + capacité FO + n° de branche Seront ajoutés le ou les µmodules qui alimentent le tiroir  Pour les SRO :  "NRO/TR XX XXXX" issu du cb_codeext;  Seront ajoutés le ou les µmodules qui alimentent le tiroir  ==> voir règles d'étiquetage   |
| `ti_ba_code` | `VARCHAR(254)` (`REFERENCES t_baie (ba_code)`) | Identifiant unique contenu dans la table BAIE | N | N | O | O | O | O | O |  |
| `ti_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du propriétaire du tiroir. | N | N | N | N | N | N | N |  |
| `ti_etat` | `VARCHAR(3)` (`REFERENCES l_etat_type (code)`) | Etat du TIROIR | N | N | N | N | N | N | N |  |
| `ti_type` | `VARCHAR(10)` (`REFERENCES l_tiroir_type (code)`) | Type du contenant selon qu'il s'agisse d'un TIROIR ou d'une TETE DE CABLE. | N | N | O | O | O | O | O |  |
| `ti_rf_code` | `VARCHAR(254)` (`REFERENCES t_reference (rf_code)`) | Identifiant de la référence du tiroir dans la table référence. | N | N | O | O | O | O | O |  |
| `ti_taille` | `NUMERIC` | Taille du tiroir en nombre de U | N | N | N | N | N | N | N |  |
| `ti_placemt` | `NUMERIC` | Position du tiroir en "nombre de U" (Le U numéro 1 est situé en bas de la BAIE) | N | N | N | N | N | N | N |  |
| `ti_localis` | `VARCHAR(254)` | Informations de localisation du tiroir | N | N | N | N | N | N | N |  |
| `ti_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `ti_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | n | N | O | O | O | O | O |  |
| `ti_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `ti_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `ti_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | C | C | C |  |
| `ti_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_zcoax`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `geom` | `geometry(MultiPolygon,2154)` | Surface de couverture | N | N | N | N | N | N | N |  |
| `zc_code` | `VARCHAR(254)` | Code la zone de couverture de service cablé (COAX). | N | N | N | N | N | N | N |  |
| `zc_codeext` | `VARCHAR(254)` | Code de la zone dans une base de données externe. | N | N | N | N | N | N | N |  |
| `zc_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code interne hérité du Noeud. Permet de rattacher la zone à un noeud si l'information est disponible. | N | N | N | N | N | N | N |  |
| `zc_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | N | N | N | N | N | N | N |  |
| `zc_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | N | N | N | N | N | N | N |  |
| `zc_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | N | N | N | N | N | N |  |
| `zc_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `zc_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du propriétaire du site. | N | N | N | N | N | N | N |  |
| `zc_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du gestionnaire du site. | N | N | N | N | N | N | N |  |
| `zc_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | N | N | N | N | N | N | N |  |
| `zc_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `zc_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `zc_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | N | N | N | N | N |  |
| `zc_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | N | N | N |  |
| `zc_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `zc_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | N | N | N |  |
| `zc_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_zdep`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `geom` | `geometry(MultiPolygon,2154)` | Surface de couverture | N | N | N | N | N | N | N |  |
| `zd_code` | `VARCHAR(254)` | Code de zone de déploiement d'infrastructure. | n | N | N | N | N | N | N |  |
| `zd_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code interne hérité du Noeud | N | N | N | N | N | N | N |  |
| `zd_zs_code` | `VARCHAR(254)` (`REFERENCES t_zsro (zs_code)`) | Code de la Zone arrière de SRO parente s'il s'agit d'une subdivision. | N | N | N | N | N | N | N |  |
| `zd_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | n | N | N | N | N | N | N |  |
| `zd_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | N | N | N | N | N | N | N |  |
| `zd_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | N | N | N | N | N | N |  |
| `zd_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `zd_prop` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du propriétaire du site. | N | N | N | N | N | N | N |  |
| `zd_gest` | `VARCHAR(20)` (`REFERENCES t_organisme (or_code)`) | Identifiant du gestionnaire du site. | N | N | N | N | N | N | N |  |
| `zd_statut` | `VARCHAR(3)` (`REFERENCES l_statut (code)`) | Phase d'avancement | N | N | N | N | N | N | N |  |
| `zd_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `zd_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `zd_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | N | N | N | N | N | N |  |
| `zd_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | N | N | N |  |
| `zd_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `zd_abddate` | `DATE` | Date d'abandon de l'objet | n | N | N | N | N | N | N |  |
| `zd_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |

---

## `t_znro`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `zn_code` | `VARCHAR(254)` | Code la zone arrière de NRO | O | O | O | O | O | O | O |  |
| `zn_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code interne hérité du Noeud | O | O | O | O | O | O | O |  |
| `zn_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | O | O | O | O | O | O | O | DP + n° dpt sur 2 digits (ex : DP71) |
| `zn_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | O | O | O | O | O | O | O | Code NRO du département (ex : NRO39086TNI) |
| `zn_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | N | N | N | N | N | N |  |
| `zn_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `zn_nroref` | `VARCHAR(15)` | Référence du NRO (Interop CPN) | O | O | O | O | O | O | O | Sera renseigné avec la référence universelle du NRO (référence des départements et de la MOE) |
| `zn_nrotype` | `VARCHAR(7)` (`REFERENCES l_nro_type(code)`) | Type de NRO (Interop CPN). | N | N | N | N | N | N | N | Valeur "NRO-PON-PTP" par défaut a renseigner  |
| `zn_etat` | `VARCHAR(2)` (`REFERENCES l_nro_etat(code)`) | Etat d'avancement du NRO (Interop CPN) | N | N | N | N | N | N | N | Le fermier le renseigne toujours à PLANIFIE jusqu'à la MAD PM. Fournit DEPLOYE en bien de retour après la MAD PM. Puis toujours DEPLOYE par la suite.  |
| `zn_etatlpm` | `VARCHAR(2)` (`REFERENCES l_nro_etat(code)`) | Etat d'avancement du lien entre le NRO et le SRO (Interop CPN). | N | N | N | N | N | N | N |  |
| `zn_datelpm` | `DATE` | Date d'installation du lien entre le NRO et le SRO (Interop CPN) | N | N | N | N | N | N | N |  |
| `zn_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `zn_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `zn_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | O | O | O | O |  |
| `zn_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | C | C | C | C | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `zn_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `zn_abddate` | `DATE` | Date d'abandon de l'objet | C | C | C | C | C | C | C |  |
| `zn_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |
| `geom` | `geometry(MultiPolygon,2154)` | Surface de couverture | O | O | O | O | O | O | O |  |

---

## `t_zpbo`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `zp_code` | `VARCHAR(254)` | Code la zone arrière de PBO | N | O | O | N | N | O | O |  |
| `zp_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code interne hérité du Noeud | N | O | O | N | N | O | O |  |
| `zp_zs_code` | `VARCHAR(254)` (`REFERENCES t_zsro (zs_code)`) | Code de la Zone Arrière de SRO correspondante. | N | O | O | N | N | O | O |  |
| `zp_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | N | O | O | N | N | O | O | DP + n° dpt sur 2 digits (ex : DP71) |
| `zp_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | N | O | O | N | N | O | O | Code NRO du département (ex : NRO39086TNI) |
| `zp_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | N | O | O | N | N | O | O | Code SRO (ex : NRO39086TNI_11) |
| `zp_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `zp_capamax` | `INTEGER` | Capacité en nombre de lignes. | N | N | N | N | N | N | N |  |
| `zp_comment` | `VARCHAR(254)` | Commentaire | N | N | N | N | N | N | N |  |
| `zp_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `zp_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | N | O | O | N | N | O | O |  |
| `zp_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | N | N | N | N | N | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `zp_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `zp_abddate` | `DATE` | Date d'abandon de l'objet | N | N | N | N | N | C | C |  |
| `zp_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |
| `geom` | `geometry(MultiPolygon,2154)` | Surface de couverture | N | O | O | N | N | O | O |  |

---

## `t_zpbo_patch201`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `zp_code` | `VARCHAR(254)` (`REFERENCES t_zpbo (zp_code)`) | Code unique du ZPBO | N | O | O | N | N | O | O |  |
| `zp_bp_code` | `VARCHAR(254)` (`REFERENCES t_ebp(bp_code)`) | Code unique de l'EBP | N | O | O | N | N | O | O |  |

---

## `t_zsro`

| Attribut | Type | Définition | Consultation Lot | PRO | EXE Distri | EXE Transp | REC Transp | REC Distri | REC V2 | Règles de gestion |
|---|---|---|---|---|---|---|---|---|---|---|
| `zs_code` | `VARCHAR(254)` | Code la zone arrière de SRO | O | O | O | O | O | O | O |  |
| `zs_nd_code` | `VARCHAR(254)` (`REFERENCES t_noeud (nd_code)`) | Code interne hérité du Noeud | O | O | O | O | O | O | O |  |
| `zs_zn_code` | `VARCHAR(254)` (`REFERENCES t_znro (zn_code)`) | Code de la Zone Arrière de NRO correspondante. | O | O | O | O | O | O | O |  |
| `zs_r1_code` | `VARCHAR(100)` | Code d'un référencement du réseau 1 (plaque, dsp, BM, etc.) | O | O | O | O | O | O | O | DP + n° dpt sur 2 digits (ex : DP71) |
| `zs_r2_code` | `VARCHAR(100)` | Code d'un référencement du réseau 2 (poche, tronçon, etc.) | O | O | O | O | O | O | O | Code NRO du département (ex : NRO39086TNI) |
| `zs_r3_code` | `VARCHAR(100)` | Code d'un référencement du réseau 3 (secteur, etc.) | O | O | O | O | O | O | O | Code SRO (ex : NRO39086TNI_11) |
| `zs_r4_code` | `VARCHAR(100)` | Code d'un référencement du réseau 4 | N | N | N | N | N | N | N |  |
| `zs_refpm` | `VARCHAR(20)` | IPE : Référence PM propre à chaque OI et pérenne. La reference PM est obligatoire dès lors que le PM est en cours de déploiement et ne peut apparaître avant. La référence PM est celle du PM de Regroupement dans le cas de plusieurs PMTechniques rattachés au même PM. | O | O | O | O | O | O | O | Sera renseigné avec la référence universelle du SRO (référence des départements et de la MOE) Exemple : NRO71512SGI_11 |
| `zs_etatpm` | `VARCHAR(2)` (`REFERENCES l_sro_etat(code)`) | IPE : Doit être renseigné dès lors que le PM apparait dans l'IPE. | O | O | O | O | O | O | O | Sera fourni à la consultation et en EXE avec la valeur "PL". Sera fourni en bien de retour suite MAD PM avec la valeur "déployé". Autres valeurs de statuts a renseigner par ETR.  |
| `zs_dateins` | `DATE` | IPE : Date d'installation du PM, qu'il soit intérieur ou extérieur. Cette date correspond à la date de passage à l'état déployé du PM. Cette date est obligatoire dès lors qu'une referencePM existe. Elle est prévisionnelle si EtatPM est "en cours de déploiement" et effective si EtatPM est "déployé" | N | N | N | N | N | O | O | NOK à l'EXE |
| `zs_typeemp` | `VARCHAR(3)` (`REFERENCES l_sro_emplacement(code)`) | IPE : Ce champ permet de décrire la localisation physique du PM (façade, poteau, chambre, intérieur…) et/ou type de PM (shelter, armoire de rue, en sous-sol….). | N | N | N | N | N | N | N |  |
| `zs_capamax` | `INTEGER` | IPE : Capacité maximum théorique du SRO. | O | O | O | O | O | O | O | Armoire 2*28U = Capamax = 600 Armoire 2*40U = Capamax = 800 La publication de lot ne pourra se faire qu'à partir de l'EXE SRO et Pro distri.   |
| `zs_ad_code` | `VARCHAR(254)` (`REFERENCES t_adresse(ad_code)`) | IPE : Code de l'adresse dans la table adresse. | N | N | N | N | N | N | N |  |
| `zs_typeing` | `VARCHAR(254)` | IPE : Champ décrivant le type d'ingénierie (mono, bi, quadri) tel que décrit dans le contrat de l'OI. Cette valeur fait référence aux STAS de l'opérateur d'immeuble. L'information contenue dans ce champ est utilisée pour la facturation et renvoie aux listes autorisées dans le contrat. | N | N | N | N | N | N | N |  |
| `zs_nblogmt` | `INTEGER` | IPE : Ce champ correspond au nombre total de logements dans la zone arrière du PM Technique (c'est à dire nombre de logements total : ciblé, signé, déployé). Dans le cadre d'un PM Intérieur il correspond à l'ensemble des logements raccordables. Dans le cadre d'un PM Extérieur, il correspond à l'ensemble des logements dans la zone arrière du PM, quel que soit leur statut   | O | O | O | O | O | O | O |  |
| `zs_nbcolmt` | `INTEGER` | IPE : Nombre de colonnes montantes associées au PM dans les cas de PM Intérieur. Il est facultatif et renseigné par certains l'opérateur d'immeuble à des fins de facturation. | N | N | N | N | N | N | N |  |
| `zs_datcomr` | `DATE` | IPE : Date à laquelle le raccordement effectif d'un client final à ce PM est possible du point de vue de la réglementation. Cette date équivaut à la date à laquelle le PM est passé déployé avec une première mise à disposition faite aux opérateurs commerciaux + 3 mois. | N | N | N | N | N | N | N |  |
| `zs_actif` | `BOOLEAN` | IPE : doit indiquer s'il y a de l'electricité au PM pour permettre à un opérateur commercial d'y disposer des équipements actifs. Répond à une demande de la réglementation de pouvoir proposer de l'actif au PM. | N | N | N | N | N | N | N | ok Fermier pour mettre à N car que du passif dans les PM  |
| `zs_datemad` | `DATE` | IPE : permet de renseigner la date de Première Mise à Disposition du PM à un opérateur commercial. Une fois cette première mise à disposition passée, cette date n'évolue pas. En cas d'absence d'opérateur commercial lors de l'installation du PM, cette date est valorisée avec la date d'installation du PM (contenu du champ DateInstallationPM). Cette date fait démarrer le délai réglementaire de 3 mois avant mise en service commerciale du PM. | N | N | N | N | N | N | N |  |
| `zs_accgest` | `BOOLEAN` | IPE : permet de savoir si un accord du gestionnaire d'immeuble (copropriété, syndic, etc.) est nécessaire ou non pour aller raccorder l'adresse. | N | N | N | N | N | N | N |  |
| `zs_brassoi` | `BOOLEAN` | IPE : Ce commentaire a pour objectif d'informer les OC que sur ce PM, les OI n'autorisent que les brassages par lui meme (OI). Ce champ permet à l'OC de préparer des commandes d'acces de formats différentes. | N | N | N | N | N | N | N |  |
| `zs_comment` | `VARCHAR(254)` | Commentaire | O | N | N | N | N | N | O | MOE fournira en amont un fichier Excel avec NRO/SRO/longueur en mètres qui permettra à l'Exploitant d'ajouter cette valeur  |
| `zs_geolsrc` | `VARCHAR(254)` | Source de la géolocalisation pour préciser la source si nécessaire | N | N | N | N | N | N | N |  |
| `zs_creadat` | `TIMESTAMP` | Date de création de l'objet en base (peut être calculé) | O | O | O | O | O | O | O |  |
| `zs_majdate` | `TIMESTAMP` | Date de la mise à jour de l'objet en base (peut être calculé) | C | N | N | N | N | C | C | Obligatoire si mise à jour  - format dates accepté en injection :   . aaaa/mm/jj  . aaaa-mm-jj  . mm/jj/aaaa  . mm-jj-aaaa  - format dates restitué(respect strict de la recommandation AVICCA cad : aaaa-mm-jj |
| `zs_majsrc` | `VARCHAR(254)` | Source utilisée pour la mise à jour | N | N | N | N | N | N | N |  |
| `zs_abddate` | `DATE` | Date d'abandon de l'objet | C | N | N | N | N | C | C |  |
| `zs_abdsrc` | `VARCHAR(254)` | Cause de l'abandon de l'objet | N | N | N | N | N | N | N |  |
| `geom` | `geometry(MultiPolygon,2154)` | Surface de couverture | O | O | O | O | O | O | O |  |

---

