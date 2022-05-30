<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/addContact", name="create_contact")
     */
    public function createContact(ManagerRegistry $doctrine, Request $request): Response
    {
      $entityManager = $doctrine->getManager();

      $contact = new Contact();
//      $contact->setName('azerty');
//      $contact->setFirstname('john');
//      $contact->setAge('22');
//      $contact->setPhone('0625487565');
//      $contact->setAddress('11 chemin carbon');
//      $contact->setVille('lyon');

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('message', 'contact créer avec succès');

            return $this->redirectToRoute('home');
        }

      return $this->renderForm('home/createContact.html.twig', [
         'form' => $form
      ]);
    }


    /**
     * @Route("/contact/{contactId}", name="get_one_contact")
     */
    public function getOneContact(ManagerRegistry $doctrine, int $contactId): Response
    {
        $entityManager = $doctrine->getManager();
        $contacts = $entityManager->getRepository(Contact::class)->find($contactId);
        return $this->render('home/contact.html.twig', [
            'contact' => $contacts
        ]);
    }

    /**
     * @Route("/edit/{contactId}", name="update_contact")
     */
    public function updateContact(ManagerRegistry $doctrine, Request $request,int $contactId): Response
    {

        $entityManager = $doctrine->getManager();
        $contact = $entityManager->getRepository(Contact::class)->find($contactId);

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('message', 'contact modifier avec succès');

            return $this->redirectToRoute('home');
        }
        return $this->render('home/modifierContact.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/delete/{contactId}", name="delete_contact")
     */
    public function deleteContact(ManagerRegistry $doctrine, int $contactId): Response
    {
        $entityManager = $doctrine->getManager();
        $contact = $entityManager->getRepository(Contact::class)->find($contactId);

        $entityManager->remove($contact);
        $entityManager->flush();

        return $this->redirectToRoute('home');

    }
}
