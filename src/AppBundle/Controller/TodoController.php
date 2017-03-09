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
    $events = $this->getDoctrine()
      ->getRepository('AppBundle:Todo')
      ->findBy([], ['dueDate' => 'ASC']);

    return $this->render('todo/index.html.twig', array(
      'events' => $events));
  }

  /**
   * @Route("/todo/create", name="todo_create")reate
   */
  public function createAction(Request $request)
  {
    
    $style = 'margin-bottom:15px';
    $priorities = array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High');
    $event = new Todo;

    $event->setDueDate(new \DateTime('now'));

    $form = $this->createFormBuilder($event)
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
        'label' => 'Create event',
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

        $event->setName($name);
        $event->setCategory($category);
        $event->setDescription($description);
        $event->setPriority($priority);
        $event->setDueDate($due_date);
        $event->setCreateDate($now);

        $em = $this->getDoctrine()->getManager();

        $em->persist($event);
        $em->flush();

        $this->addFlash('notice','Event added');
        $dateParts = $this->get("CalendarUtils")->parseDateTime($event->getDueDate());
        $routeParams = array('year' => $dateParts['yearNum'], 'month' => $dateParts['monthNum']);

        return $this->redirectToRoute('todo_month_view', $routeParams);

       }


    return $this->render('todo/create.html.twig', array('form' => $form->createView()));
  }

  /**
   * @Route("/todo/edit/{id}", name="todo_edit")
   */
  public function editAction($id, Request $request)
  { 
    $event = $this->getDoctrine()
      ->getRepository('AppBundle:Todo')
      ->find($id);

    $now = new\DateTime('now');

    $event->setName($event->getName());
    $event->setCategory($event->getCategory());
    $event->setDescription($event->getDescription());
    $event->setPriority($event->getPriority());
    $event->setDueDate($event->getDueDate());
    $event->setCreateDate($now);

    $style = 'margin-bottom:15px';
    $priorities = array('Low' => 'Low', 'Normal' => 'Normal', 'High' => 'High');

    $form = $this->createFormBuilder($event)
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
        'label' => 'Update event',
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

        $event->setName($name);
        $event->setCategory($category);
        $event->setDescription($description);
        $event->setPriority($priority);
        $event->setDueDate($due_date);
        $event->setCreateDate($now);

        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('AppBundle:Todo')->find($id);

        $em->flush();

        $this->addFlash('notice','Event updated');

        $dateParts = $this->get("CalendarUtils")->parseDateTime($event->getDueDate());
        $routeParams = array('year' => $dateParts['yearNum'], 'month' => $dateParts['monthNum']);

        return $this->redirectToRoute('todo_month_view', $routeParams);

       }  

    return $this->render('todo/edit.html.twig', array(
      'event' => $event,
      'form' => $form->createView()
    ));
  }

  /**
   * @Route("/todo/details/{id}", name="todo_details")
   */
  public function detailsAction($id)
  {
    $event = $this->getDoctrine()
      ->getRepository('AppBundle:Todo')
      ->find($id);

    return $this->render('todo/details.html.twig', array(
      'event' => $event));
  }

/**
   * @Route("/todo/delete/{id}", name="todo_delete")
   */
  public function deleteAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $event = $em->getRepository('AppBundle:Todo')->find($id);

    $em->remove($event);
    $em->flush();
 
    $this->addFlash('notice','Event deleted');

    return $this->redirectToRoute('todo_month_view');
  }



  /**
   * @Route("/monthView/{year}/{month}", name="todo_month_view", defaults={"year" = null, "month" = null}))
   */
 function monthAction($year, $month)
  {
    
    $now = new\DateTime('now');
    $year = $year !== null ? intval($year) : intval($now->format('Y'));
    $month = $month !== null ? intval($month) : intval($now->format('m'));

    $allEvents = $this->getDoctrine()
      ->getRepository('AppBundle:Todo')
      ->findAll();

    $calendar = $this->get("CalendarUtils")->getCalendarMonthArray($month,$year);
    $monthName = $this->get("CalendarUtils")->getNameOfMonth($month);
    $daysOfWeek = $this->get("CalendarUtils")->getDaysOfWeekShort();
    $nextMonthYear = $this->get("CalendarUtils")->getNextMonthYear($month,$year);
    $prevMonthYear = $this->get("CalendarUtils")->getPrevMonthYear($month,$year);    
    $filteredEvents = $this->get("CalendarUtils")->filterThisMonthsEvents($allEvents, $month, $year);
    $formattedEvents = $this->get("CalendarUtils")->formatEvents($filteredEvents);

    return $this->render('todo/monthView.html.twig', array(
      'year' => $year,
      'month' => $month,
      'events' => $formattedEvents,
      'daysOfWeek' => $daysOfWeek,
      'monthName' => $monthName,
      'prevMonthYear' => $prevMonthYear,
      'nextMonthYear' => $nextMonthYear,
      'calendar' => $calendar));
  }

 
}
