<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Todo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TodoController extends Controller
{
  /**
   * @Route("/", name="todo_list")
   */
  public function listAction()
  {
    $todos = $this->getDoctrine()
      ->getRepository('AppBundle:Todo')
      ->findAll();

    return $this->render('todo/index.html.twig', array(
      'todos' => $todos));
  }

  /**
   * @Route("/todo/create", name="todo_create")reate
   */
  public function createAction(Request $request)
  {
    
    $style = 'margin-bottom:15px';
    $priorities = array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High');
    $todo = new Todo;

    $form = $this->createFormBuilder($todo)
      ->add('name', TextType::class, array(
        'attr' => array(
          'class' => 'form-control','style' => $style
          )
      ))   
      ->add('category', TextType::class, array(
        'attr' => array(
          'class' => 'form-control','style' => $style
        )
      ))   
      ->add('description', TextareaType::class, array(
        'attr' => array(
          'class' => 'form-control','style' => $style
        )
      ))   
      ->add('priority', ChoiceType::class, array(
        'choices' => $priorities,
        'attr' => array(
          'class' => 'form-control','style' => $style
        )
      ))   
      ->add('due_date', DateTimeType::class, array(
        'attr' => array(
          'class' => '','style' => $style
          )
      ))
      ->add('save', SubmitType::class, array(
        'label' => 'Create Todo',
        'attr' => array(
          'class' => 'btn btn-primary','style' => $style
          )
      ))
      ->getForm();   

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $name = $form['name']->getData();
        $category = $form['category']->getData();
        $description = $form['description']->getData();
        $priority = $form['priority']->getData();
        $due_date = $form['due_date']->getData();

        $now = new\DateTime('now');

        $todo->setName($name);
        $todo->setCategory($category);
        $todo->setDescription($description);
        $todo->setPriority($priority);
        $todo->setDueDate($due_date);
        $todo->setCreateDate($now);

        $em = $this->getDoctrine()->getManager();

        $em->persist($todo);
        $em->flush();

        $this->addFlash('notice','Todo added');

        return $this->redirectToRoute('todo_list');

       }


    return $this->render('todo/create.html.twig', array('form' => $form->createView()));
  }

  /**
   * @Route("/todo/edit/{id}", name="todo_edit")
   */
  public function editAction($id, Request $request)
  { 
    return $this->render('todo/edit.html.twig');
  }

  /**
   * @Route("/todo/details/{id}", name="todo_details")
   */
  public function detailsAction($id)
  {
    $todo = $this->getDoctrine()
      ->getRepository('AppBundle:Todo')
      ->find($id);

    return $this->render('todo/details.html.twig', array(
      'todo' => $todo));
  }

  /**
   * @Route("/monthView/{year}/{month}", name="todo_month_view")
   */
 function monthAction($year, $month)
  {
    $year = intval($year);
    $month = intval($month);

    $allEvents = $this->getDoctrine()
      ->getRepository('AppBundle:Todo')
      ->findAll();

    $calendar = $this->get("CalendarUtils")->getCalendarMonthArray($month,$year);
    $monthName = $this->get("CalendarUtils")->getNameOfMonth($month);
    $daysOfWeek = $this->get("CalendarUtils")->getDaysOfWeekShort();
    $filteredEvents = $this->get("CalendarUtils")->filterThisMonthsEvents($allEvents, $month, $year);
    $events = $this->get("CalendarUtils")->formatEvents($filteredEvents);

    return $this->render('todo/monthView.html.twig', array(
      'year' => $year,
      'month' => $month,
      'events' => $events,
      'daysOfWeek' => $daysOfWeek,
      'monthName' => $monthName,
      'calendar' => $calendar));
  }

 
}
