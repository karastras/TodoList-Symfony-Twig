var app = {
    // Méthode qui initialise notre objet "app"
    init: function() {
        console.log('app initialisée');
        // Ajout écouter 'click' sur image du cookie
        $('.list-group-item form button').on('click', app.deleteTask);
    },
    // Méthode qui va chercher le message
    deleteTask: function(e) {
        // Court-circuite l'envoi du form
        e.preventDefault();

        console.log('bouton de form cliqué');
        // Log de l'event "e" récupéré par la fonction
        var btnSpan = $(e.target);
        console.log(btnSpan);
        // La liste se trouve 2 parents plus
        // Il y a peut-être plus safe mais pour le moment ça fera l'affaire :)
        var buttonForm = btnSpan.parent().parent();
        var listItem = buttonForm.parent();

        var taskId = listItem.attr('data-id');

        var deleteURL = buttonForm.attr('action');
        // Envoi de la requête ajax au serveur
        $.ajax(
            // la variable 'deleteURL' est définie via Twig
            // list.html.twig (via une variable JS)
            deleteURL,
            {
                method: 'POST',
                data: {
                    'id': taskId
                }
            }
        // Ecouteur du retour de la requête en cas de succès
        ).done(function(data) {
            // data correspond au contenu renvoyé par la réponse
            console.log(data);
            // On supprime la ligne du DOM (la tâche n'existe plus en back)
            if(data.error == false) {
                // On y accède via le sélecteur CSS .list-group-item[data-id=2]
                $('.list-group-item[data-id=' + data.id + ']').remove();
            }
        });
    }
}
// le $ fait référence à la libraire JQuery => ajouter le cdn dans base.html.twig
$(app.init());
