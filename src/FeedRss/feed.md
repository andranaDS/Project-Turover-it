
# FLux Rss Free-Work (Décembre 2021)

Selon le listing de Décembre 2021 fourni par Sophie Monard, les flux Rss de Carrière-Info qui doivent être en activité sont les suivants:  
- Neuvoo
- Jobijoba
- Pole Emploi
- MeteoJob Rhones Alpes
- JobRapido
- Indeed
- Linkedin
- Joblift
- Jooble
- MeteoJob (Est utilisé pour trois prestataires)
  - Jobsora
  - WhatJob
  - JobTome

## Comportement 
1. Le Controller **FeedRssController** est appelé lorsqu'on se rend sur un lien de flux Rss.
2. Si le flux existe déjà, il renvoie ce flux comme Response.
3. Si le flux n'existe pas, il lance **FeedRssCommand** qui va générer un flux et renvoie le flux en Response.

## Génération d'un flux
1. **FeedRssCommand** récupère le nom du partenaire
   - Traitement du nom : "job-tome" dans la Request devient "jobTome"  
2. **FeedRssCommand** appelle **JobPostingDTOFactory** pour générer le flux correspondant.
   - On récupère tous les **JobPostings**
   - On crée un tableau et on y insère des **JobsPostingDTO** correspondant au flux
   - On normalize ce tableau d'objet en tableau tout court
   - On passe cette donnée au template correspondant

## Créer un feed Rss
1**JobPostingDTOFactory** :
   - Ajouter le slug du flux dans **PARTNERS**
   - Ajouter la configuration dans **getConfigForFeed**
     - *'template'* désigne le template de chaque item **JobPosting** dans le Flux (OBLIGATOIRE)
     - *'rootName'* désigne le nom de la balise qui contient les **JobPostings** (ex: Jobs, Offres, Source, etc.) (OBLIGATOIRE)
     - *'rootNameParams'* désigne les attributs de rootName
     - *'xmlParams'* désigne les attributs de la balise XML
     - *'fieldsBeforeList'* désigne les balises hors de la liste des **JobPostings*
2. Ajouter les conditions à la requête dans **getJobPostings** 
3. Ajouter les paramètres pour Google dans **UtmRssParameters**
4. Créer un fichier **JobPostingNOMDUPARTNERDTO.php**
    - Doit implémenter **JobPostingDTOInterface**
    - L'ordre des GETTERS détermine l'ordre dans lequel les éléments seront automatiquement affichés si on utilise le template "dynamic"
    - La méthode **getNotRequiredFields** permet au Template de ne pas insérer les champs qui contenu dans ce tableau et qui sont vides.
    - La méthode **getParamNameElementFlux** permet de nommer le JobPosting dans le FLux (ex: Offre, Job, etc.)


A Statuer :
- Les TAGS Offre Premium Interne/Externe dans de nombreux flux
- La date d'expiration d'une offre Le TAG et Type de Contrat dans JobPostingJobijobaDTO
- Temps des CRONS et impact sur les requêtes des JobPosting
- Traitement des entreprises désactivées selon le Feed. Pas de variable "Désactivée"