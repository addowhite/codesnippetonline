<?php
require '../infra.php';
echo json_encode($db->search_snippets($_GET['user_id'], $_GET['language'], 'public', $_GET['title']));
?>