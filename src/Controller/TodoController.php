<?php

namespace App\Controller;

use App\Model\TodoModel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends AbstractController
{
    /**
     * Liste des tâches
     *
     * @Route("/todos", name="todo_list", methods={"GET"})
     */
    public function todoList()
    {
        $todos = TodoModel::findAll();

        return $this->render('todo/list.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * Affichage d'une tâche
     *
     * @Route("/todo/{id}", name="todo_show", requirements={"id" = "\d+"}, methods={"GET"})
     */
    public function todoShow($id)
    {
        $todo = TodoModel::find($id);
        if ($todo === false) {
            // Si la tâche dont l'identifiant est $id n'existe pas
            // je retroune un message d'erreur 404
            throw $this->createNotFoundException("La tâche n° $id n'existe pas");
        }

        return $this->render('todo/single.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * Changement de statut
     *
     * @Route("/todo/{id}/{status}", name="todo_set_status", requirements={"id" = "\d+", "status" = "^done|undone$"}, methods={"GET", "POST"})
     */
    public function todoSetStatus($id, $status)
    {
        // 1) On récupère les données à sauvegarder : $id, $status

        // 2) On appelle la méthode setStatus($id, $status) du TodoModel
        $todoModel = new TodoModel();
        $result = $todoModel->setStatus($id, $status);

        // 3) On v&érifie que tout s'est bien passé
        if ($result === false) {
            // Si la tâche dont l'identifant est $id n'existe pas 
            // je retourne un message d'erreur 404
            throw $this->createNotFoundException("La tâche n° $id n'existe pas");
        } else {            
            // 4) On appelle un flash message
            $this->addFlash('success', "la tâche n° $id a bien été mise à jour");
            
            // 5) redirection vers la liste de toutes les tâches
            return $this->redirectToRoute('todo_list');            
        }
    }

    /**
     * Ajout d'une tâche
     *
     * @Route("/todo/add", name="todo_add", methods={"POST"})
     * 
     * La route est définie en POST parce qu'on veut ajouter une ressource sur le serveur
     */
    public function todoAdd(Request $request)
    {
        //on fait une injection de dépendance de la classe Request pour récupérer
        //des données soumises en POST via la propriété publique $request->request (ParameterBag)
        //un ParameterBag propose une méthode "get" qui donne accès aux données transmises via la méthode
        // 1) On récupère la tache à ajouter soumise ne POST
        $newTask = $request->request->get('task');

        //Si je devais récupérer cette information via la méthode HTTP GET
        // $newTask = $request->query->get('task')

        // 2) je rajoute la nouvelle tâche à la liste
        // La gestion des données est assurée par le Model
        // Si méthode classique (non static)
        // $model = $new TodoModel();
        // $model->add($newTask)

        TodoModel::add($newTask);
        
        // 3) Redirection vers la liste des tâches
        return $this->redirectToRoute('todo_list');
    }

    /**
     * Méthode permettant la suppression d'une tâche
     * 
     * @Route("/todo/delete/{id}", name="todo_delete", methods={"POST"})
     * @return void
     */
    public function todoDelete($id)
    {
        // Suppression de la tâche : retourne true si ok, et false si la tâche n'existe pas
        $result = TodoModel::delete($id);

        if ($result === false) {
            // Si la tâche dont l'identifant est $id n'existe pas 
            // je retourne un message d'erreur 404
            throw $this->createNotFoundException("La tâche n° $id n'existe pas");
        } else {   
            // La tâche existe et la suppression s'est bien passée
            $this->addFlash(
                'notice',
                'Votre tâche est supprimée!'
            );
            return $this->redirectToRoute('todo_list');
        }
    }

    /**
     * Remise à zéro des tâches
     *
     * @Route("/todo/reset", name="todo_reset", methods={"GET"})
     * 
     * @return void
     */
    public function todoReset()
    {
        TodoModel::reset();

        $this->addFlash(
            'success',
            'Les tâches ont bien été réinitialsées!'
        );
        return $this->redirectToRoute('todo_list');
    }
}
