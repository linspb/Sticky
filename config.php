<?PHP

add_action('addMenuLoggedIn', 'addStickyMenu');
add_action('addHeadTags', 'addStickyTags');

function addStickyMenu() {
    echo "| <a href='#' id='addNoteButton'>add sticky</a> ";
}

function addStickyTags() {
    $path = array_pop(explode(DIRECTORY_SEPARATOR, __DIR__));
    echo '<link rel="stylesheet" type="text/css" href="' . $GLOBALS['INSTALL_DIR_HTML'] . '/plugins/' . $path . '/css/notes.css" media="screen" />';
    echo '<script type="text/javascript" src="' . $GLOBALS['INSTALL_DIR_HTML'] . '/plugins/' . $path . '/js/script.js"></script>';
    echo '<script>';
    echo 'window["stickyPath"] = "' . $GLOBALS['INSTALL_DIR_HTML'] . '/plugins/' . $path . '";';
    echo '</script>';
}

?>
