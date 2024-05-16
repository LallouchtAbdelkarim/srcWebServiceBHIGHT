<?php


namespace App\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;

class MessageService
{
    public function checkMessage($CODEERROR)
    {
         $listMessage = array(
         "ERROR-AUTHENTICATION" => "Username ou mot de passe incorrect !",
         "ERROR-NO-ACCOUNT" => "Aucun compte n\'as été trouvé, Voulez-vous créer un compte ?",
         "ERROR-EMPTY-PARAMS" => "Veuillez vérifier vos informations, un des champs est vide !",
         "OK|NO-FILE" => "",
         "ERROR_EXCEPETION" => "Une erreur s'est produite",
         "ERROR" => "Une erreur s'est produite",
         "NOT-SAME-USER" => "Ce n'est pas même utilisateur!",
         "OK" => "Opération effectuée",
         "EXIST" => "Le existe déjà",
         "NOT_EXIST_M" => "Le model n’existe pas",
         "NOT_EXIST_D" => "Donneur ordre n’existe pas",
         "NOT_EXIST" => "N'existe pas",
         "NO-USER" => "Les informations renseignées ne sont pas correctes nous vous invitons à nous contacter pour plus d'information !",
         "ERROR-ADD" => "Impossible d'ajouter la xxx, veuillez réessayer !",
         "ERROR-FILE" => "Veuillez vérifier les pièces jointes requises !",
         "INVALIDE-NOM" => "Veuillez vérifier le nom ou le prénom !",
         "INVALID-PARAMS" => "Veuillez vérifier les informations renseignées !",
         "ERROR-DOCUMENT" => "",
         "ERROR-PATH" => "ERROR",
         "EMPTY-DATA" => "Veuillez vérifier vos informations, un des champs est vide !",
         "ERROR-PROV" => "Impossible de demander la résiliation, vous avez des impayés !",
         "NO-IDENT" => "Veuillez vérifier le XXX !",  
         "NO-AUTH" => "Erreur d'authorization !", 
         "ERROR_FILE_EXTENSION" =>  "Extension de fichier incorrecte, importer un fichier csv SVP !!", 
         "EMPTY_FILE"=>"Importer un fichier et remplir tous les champs SVP !!",
         "ERROR_TRANSFERT_FILE"=>"Erreur lors du transfert du fichier !!",
         "ERROR_EXTENSION"=>"Extension dde fichier incorrecte, importer un fichier csv SVP !!",
         "IMPORT_REQUIRED"=>"Un import est oubliguatoire",
         "Invalid JWT Token"=>"Invalid JWT Token",
         "ENTETE_VIDE"=>"Les entêtes de colonnes doivent être unique et non vides !!",
         "ENTETE_IDENTIQUE"=>"Les entêtes de colonnes doivent être unique et non vides !!",
         "EMPTY_REGLE"=>"Veuillez vérifier vos règles !",
         "AUCUN_DOSSIER"=>"Aucun dossier ne trouve !",
         "ERREUR"=>"Une erreur s'est produite",
         "NOT_EXIST_ELEMENT"=>"Cet élément n'existe pas !",
         "ELEMENT_DEJE_EXIST"=>"Cet élément déja existe !",
         "PASSWORD_MATCH"=>"Mot de passe 1 et mot de passe 2 ne correspondent pas",
         "CIN_OUBLIGATOIRE"=>"CIN du débiteur obligatoire !!",
         "INTEGRATION_NOT_EXIST"=>"Cette intégration n'existe pas !",
         "TITRE_DEJE_EXIST"=>"Cet titre déja existe !",
         "Expired JWT Token"=>"Expired JWT Token",
         "ERROR_SEGMENTATION"=>"Erreur dans la sélection de segmentation",
         "DONNEUR_DEJA_EXIST"=>"Donneur ordre déja exist !",
         "ERROR_DATE"=>"Erreur date !",
         "ERROR_SAISAIE"=>"Erreur saisaie !",
         "NUM_CREANCE_IS_EMPTY"=>"Veuillez sélectionner numéro de créance !",
         "REQUIRED_DEBITEUR"=>"Veuillez remplir tous les champs de débiteur !",
         "REQUIRED_CREANCE"=>"Veuillez remplir tous les champs de créance !",
         "ID_DEBITEUR_IS_EMPTY"=>"Veuillez sélectionner ID debiteur !",
         "GARANTIE_IS_EMPTY"=>"Veuillez sélectionner un champ de garantie !",
         "DETAIL_FINANCEMENT_IS_EMPTY"=>"Veuillez sélectionner un champ de detail financement !",
         "PROC_IS_EMPTY"=>"Veuillez sélectionner un champ de procédure judicaire !",
         "QUEUE_LIE_WORKFLOW"=>"Impossible de définir cette fonction !",
         "ERROR_IMPORT"=>"Une erreur s'est produite",
         "ERROR_FILES"=>"Le nombre de lignes n'est pas le même pour d'autre fichier",
         "CIN_DEBITEUR_OU_RS_IS_EMPTY"=>"Raison sociale OU cin débiteur est oubliguatoire",
         "ERROR_CREANCE"=>"",
         "ERROR_ECHEANCIERS"=>"Une erreur s'est produite",
         "ONE_TEL"=>"Merci d'ajouter notre téléphone activve",
         "ERROR_DURRE"=>"Veuillez vérifier votre champ du durée de gestion",
         "REQUIRED_DOSSIER"=>"Veuillez remplir tous les champs de dossier !",
         "ERROR-SEG"=>"Veuillez vérifier la segmentation sélectionnée",
         "ERROR-SEG1"=>"Veuillez vérifier la segmentation sélectionnée",
         "EMPTY-DATA-ACTIVITY"=>"Veuillez vérifier votre schéma !"
        );
        return $listMessage[$CODEERROR];
    }
}