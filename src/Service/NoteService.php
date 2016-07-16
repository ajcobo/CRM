<?php

use ChurchCRM\Note;
use ChurchCRM\NoteQuery;
use ChurchCRM\PersonQuery;


class NoteService
{

  function addNote($personID, $familyID, $private, $text, $type = "system")
  {
    requireUserGroupMembership("bNotes");

    $note = new Note();
    $note->setPerId($personID);
    $note->setFamId($familyID);
    $note->setPrivate($private);
    $note->setText($text);
    $note->setType($type);

    $note->setDateEntered(new DateTime());
    $note->setEnteredBy($_SESSION['iUserID']);

    $note->save();

  }

  function updateNote($noteId, $private, $text)
  {
    requireUserGroupMembership("bNotes");

    $note = NoteQuery::create()->findPk($noteId);
    $note->setPrivate($private);
    $note->setText($text);

    $note->setDateLastEdited(new DateTime());
    $note->setEditedBy($_SESSION['iUserID']);

    $note->save();
  }

  // Get the notes for this person
  function getNotesByPerson($personId, $admin)
  {
    $notes = NoteQuery::create()->findByPerId($personId);
    return $this->convertNotes($notes, $admin);
  }

  function getNotesByFamily($familyId, $admin)
  {
    $notes = NoteQuery::create()->findByFamId($familyId);
    return $this->convertNotes($notes, $admin);
  }

  function convertNotes($notes, $admin)
  {
    $notesArray = array();
    foreach ($notes as $rawNote) {
      // if the user is not admin, ensure the note is not private or it is created by current user
      if ($admin || $rawNote->isVisable($_SESSION['iUserID'])) {

        $note['id'] = $rawNote->getId();
        $note['private'] = $rawNote->getPrivate();
        $note['text'] = $rawNote->getText();
        $note['type'] = $rawNote->getType();

        if ($rawNote->getDateLastEdited() != null) {
          $note['lastUpdateDatetime'] = $rawNote->getDateLastEdited()->format('Y-m-d H:i:s');
          $note['lastUpdateById'] = $rawNote->getEditedBy();
        } else {
          $note['lastUpdateDatetime'] = $rawNote->getDateEntered()->format('Y-m-d H:i:s');
          $note['lastUpdateById'] = $rawNote->getEnteredBy();
        }

        $person = PersonQuery::create()->findPk($note['lastUpdateById']);

        if ($person != null) {
          $note['lastUpdateByName'] = $person->getFullName();
        } else {
          $note['lastUpdateByName'] = "unknown?";
        }
        array_push($notesArray, $note);
      }
    }
    return $notesArray;
  }

  function deleteNoteById($noteId)
  {
    requireUserGroupMembership("bNotes");
    $note = NoteQuery::create()->findPk($noteId);
    $note->delete();
  }

}
