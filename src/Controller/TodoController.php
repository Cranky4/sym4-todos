<?php

namespace App\Controller;

use App\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    /**
     * @Route("/", name="todos", methods={"GET"})
     */
    public function index(): Response
    {
        $todos = $this->getDoctrine()->getRepository(Todo::class)->findAll();

        return $this->render('todo/index.html.twig', compact('todos'));
    }

    /**
     * @Route("/todo/view/{id}", name="todo", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function view(int $id): Response
    {
        $todo = $this->find($id);

        return $this->render('todo/view.html.twig', compact('todo'));
    }

    /**
     * @Route("/todo/update/{id}", name="edit_todo", methods={"GET", "POST"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        $todo = $this->find($id);
        $form = $this->getForm($todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->addFlash('success', 'Your changes were saved!');
            return $this->redirectToRoute('todos');
        }

        return $this->render('todo/form.html.twig', [
            'form' => $form->createView(),
            'todo' => $todo
        ]);
    }

    /**
     * @Route("/todo/delete/{id}", name="delete_todo", methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $todo = $this->find($id);

        $em = $this->getDoctrine()->getManager();
        $this->addFlash('success', "Todo #{$todo->getId()} was deleted!");
        $em->remove($todo);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/todo/new", name="new_todo", methods={"GET", "POST"})
     * @return Response
     */
    public function new(Request $request): Response
    {
        $todo = new Todo();
        $todo->setIsActive(true); //TODO: move this to event listeners
        $form = $this->getForm($todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->addFlash('success', "New Todo #{$todo->getId()} was created!");
            return $this->redirectToRoute('todos');
        }

        return $this->render('todo/form.form.twig', [
            'form' => $form->createView(),
            'todo' => $todo
        ]);
    }

    protected function find(int $id): ?Todo
    {
        /** @var Todo $todo */
        $todo = $this->getDoctrine()->getRepository(Todo::class)->find($id);

        if (null === $todo) {
            throw new NotFoundHttpException('Todo not found!');
        }

        return $todo;
    }

    protected function getForm(Todo $todo): FormInterface
    {
        return $this->createFormBuilder($todo)
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('createdAt', DateTimeType::class, ['attr' => ['class' => '']])
            ->add('submit', SubmitType::class, ['attr' => ['class' => 'btn btn-primary']])
            ->getForm();
    }
}
