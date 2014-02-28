<?php

$hideEcho = TRUE;
session_start();
include './../../includes/includes.php';

switch ($_REQUEST['action']) {
    case 'init':
        ?>
        <div id='addNote' style="display: none" title="Add notes">
            <div id="previewNote" class="note yellow" style="left:10;top:45px;z-index:1">
                <div class="body"></div>
                <div class="author"></div>
                <span class="data"></span>
            </div>
            <div id="noteData"> <!-- Holds the form -->
                <form action="" method="post" class="note-form">

                    <label for="note-body">Text of the note</label>
                    <textarea name="note-body" id="note-body" class="pr-body" cols="30" rows="6"></textarea>

                    <label for="note-name">Put on</label>
                    <select name="note-name" id="note-name" class="pr-author">
                        <option value="me">My desktop</option>
                        <!-- <option value="myd">My department desktops</option> //-->
                        <optgroup label="Sent to:">
                        <?php
                            $u = Array();
                            $a = new auth();
                            $r = $a->getUserData();
                            foreach ($r as $rr) {
                                $u[trim(strtolower($rr['login']))] = "<option value=\"" . $rr['id'] . " desktop\">" . ucfirst($rr['login']) . " desktop</option>";
                            }
                            ksort($u);
                            print_r($u);
                            echo implode('', $u);
                        ?>
                        </optgroup>
                        <!-- <option value="all">All desktops</option> //-->
                    </select>    

                    <label>Color</label> <!-- Clicking one of the divs changes the color of the preview -->
                    <div class="color yellow"></div>
                    <div class="color blue"></div>
                    <div class="color green"></div>
                </form>
            </div>
        </div>
        <!-- <div id='noteMenu' style="display: none; position: absolute; top: -1000; left: -1000; z-index: 999999999">
            <ul>
               <li><a href='#'>Remind me in 1 hour</a></li>
                <li><a href='#'>Remind me in 1 day</a></li>
                <li><a href='#'>Remind me in 3 day</a></li>
                <li><a href='#'>Remind me in 1 hour & pidgin me</a></li>
                <li><a href='#'>Remind me in 1 day & pidgin me</a></li>
                <li><a href='#'>Remind me in 3 day & pidgin me</a></li>
                <li><a href='#'>Forward to ...</a></li>
                <li><a href='#' onclick='closeNoteConfirm(this); return false;'>Close</a></li>
            </ul>
            <div style='clear: both'></div>
        </div> //-->
        <?php
        break;

    case 'get':
        $a = new auth();
        if (isset($GLOBALS['userId']) && $GLOBALS['userId']) {
            $notes = db::executeQuery('SELECT * FROM `notes` WHERE name = :name AND hide = 0 AND id > :id;',
                    array('name' => $GLOBALS['userId'], 'id' => $_REQUEST['noteId']), DB_ATHENA)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($notes as $key => $note) {
                if ($notes[$key]['userId'] != $notes[$key]['name']) {
                    $r = $a->getUserData($notes[$key]['userId']);
                    $notes[$key]['text'] .= '<div style="float: right;">--' . $r[0]['login'] . '</div>';
                }
            }

            echo json_encode($notes);
        }
        break;

    case 'add':
        // Checking whether all input variables are in place:
        if (!is_numeric($_POST['zindex']) || !isset($_POST['author']) || !isset($_POST['body'])
                || !in_array($_POST['color'], array('yellow', 'green', 'blue'))) {
            die();
        }

        // Escaping the input data:
        $author = strip_tags($_POST['author']);
        switch ($author) {
            case 'me':
                $author = $GLOBALS['userId'];
                break;

            case (!!preg_match('/^[0-9]+$/', $author)):
                $author = $author;
                break;

            default:
                break;
        }

        $body = strip_tags($_POST['body']);
        $color = $_POST['color'];
        $zindex = (int)$_POST['zindex'];

        $id = db::executeQuery('INSERT INTO notes (userId, text, name, color, xyz) '
                . 'VALUES (:userId, :text, :name, :color, :xyz);',
                array('userId' => $GLOBALS['userId'], 'text' => $body, 'name' => $author,
                    'color' => $color, 'xyz' => '0x0x' . $zindex), DB_ATHENA, TRUE);

        echo $id;
        break;

    case 'position':

        db::executeQuery('UPDATE notes SET xyz = :xyz WHERE id = :id;',
                array('id' => $_REQUEST['id'], 'xyz' => (int)$_REQUEST['x'] . 'x'
                    . (int)$_REQUEST['y'] . 'x' . (int)$_REQUEST['z']), DB_ATHENA);

        break;

    case 'hide':

        db::executeQuery('UPDATE notes SET hide = 1 WHERE id = :id;',
                array('id' => $_REQUEST['id']), DB_ATHENA, TRUE);

        break;

    default:
        break;
}
?>
